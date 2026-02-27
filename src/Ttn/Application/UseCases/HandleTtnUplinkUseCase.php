<?php

namespace App\Ttn\Application\UseCases;

use App\Entity\Client\WeightsLog;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Ttn\Application\DTO\TtnUplinkRequest;
use App\Ttn\Application\InputPorts\HandleTtnUplinkUseCaseInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Ttn\Application\Services\BusinessHoursCheckerService;
use App\Ttn\Application\Services\TtnAlarmNotificationService;
use App\Ttn\Application\Services\WeightsLogService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class HandleTtnUplinkUseCase implements HandleTtnUplinkUseCaseInterface
{
    public function __construct(
        private readonly PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
        private readonly BusinessHoursCheckerService $businessHoursChecker,
        private readonly WeightsLogService $weightsLogService,
        private readonly TtnAlarmNotificationService $notificationService
    ) {
    }

    public function execute(TtnUplinkRequest $request): void
    {
        $devEui = $request->getDevEui();
        $this->logger->info('[TTN Uplink] Iniciando execute.', [
            'devEui' => $devEui,
            'deviceId' => $request->getDeviceId(),
            'voltage' => $request->getVoltage(),
            'weight_grams' => $request->getWeightGrams(),
            'weight_kg' => $request->getWeight(),
        ]);

        if (!$devEui) {
            $this->logger->error('DEV_EUI_MISSING');
            throw new \RuntimeException('DEV_EUI_MISSING');
        }

        $deviceId = $request->getDeviceId();
        if (!$deviceId) {
            $this->logger->error('DEVICE_ID_MISSING');
            throw new \RuntimeException('DEVICE_ID_MISSING');
        }

        // 1) Buscar en pool_ttn_device
        $this->logger->debug('[TTN Uplink] Buscando en pool_ttn_device', ['deviceId' => $deviceId]);
        $ttnDevice = $this->poolTtnDeviceRepository->findOneBy($deviceId);
        if (!$ttnDevice) {
            $this->logger->error('DEVICE_NOT_FOUND', ['deviceId' => $deviceId]);
            throw new \RuntimeException('DEVICE_NOT_FOUND');
        }

        $uuidClient = $ttnDevice->getEndDeviceName();
        $this->logger->debug('[TTN Uplink] uuidClient obtenido', ['uuidClient' => $uuidClient]);

        // 2) Conectarte a la BBDD del cliente
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);

        $notificationSettings = $this->notificationService->getNotificationSettings($entityManager);
        $this->logger->debug('[TTN Uplink] Configuración de alarmas cargada.', [
            'outOfHours' => $notificationSettings['out_of_hours'],
            'holidays' => $notificationSettings['holidays'],
            'batteryShelve' => $notificationSettings['battery_shelve'],
        ]);

        $scaleRepository = $entityManager->getRepository(\App\Entity\Client\Scales::class);
        $scale = $scaleRepository->findOneBy(['end_device_id' => $deviceId]);
        if (!$scale) {
            $this->logger->error('SCALE_NOT_FOUND', ['deviceId' => $deviceId]);
            throw new \RuntimeException('SCALE_NOT_FOUND');
        }

        // Porcentaje de las pilas
        $percentage = max(0, min(100, ($request->getVoltage() - 3.2) / (3.6 - 3.2) * 100));
        $this->logger->debug('[TTN Uplink] Calculado voltagePercentage', [
            'percentage' => $percentage,
        ]);

        $scale->setVoltagePercentage($percentage);
        $scale->setLastSend(new \DateTime());
        $entityManager->persist($scale);
        $this->logger->debug('[TTN Uplink] Scale guardada', ['scaleId' => $scale->getId()]);

        $entityManager->flush();

        $product = $scale->getProduct();
        if (!$product) {
            $this->logger->error('PRODUCT_NOT_FOUND', ['scaleId' => $scale->getId()]);
            throw new \RuntimeException('PRODUCT_NOT_FOUND');
        }

        $this->logger->debug('[TTN Uplink] Forzamos la inicialización de product');
        $entityManager->initializeObject($product);

        $weightRange = $product->getWeightRange() ?? 0.0;
        $this->logger->debug('[TTN Uplink] Obtenido weightRange del product', ['weightRange' => $weightRange]);

        // Obtener la tara del producto (ya está en gramos en BBDD)
        $tareGrams = $product->getTare() ?? 0.0;
        $this->logger->debug('[TTN Uplink] Tara del producto', [
            'tareGrams' => $tareGrams,
        ]);

        $mainNameUnit = $product->getMainUnit();
        if (1 == $mainNameUnit) {
            $nameUnit = $product->getNameUnit1();
        } elseif (2 == $mainNameUnit) {
            $nameUnit = $product->getNameUnit2();
        } else {
            $nameUnit = 'Kg';
        }

        // ============================================================
        // COMPARACIÓN EN GRAMOS
        // ============================================================

        // Peso bruto recibido desde TTN (incluye contenedor/tara)
        $grossWeightGrams = $request->getWeightGrams();

        // Restar la tara para obtener el peso neto del producto
        // Asegurar que el peso neto no sea negativo (protección contra errores de configuración)
        $newWeightGrams = max(0, $grossWeightGrams - $tareGrams);
        $newWeightKg = $newWeightGrams / 1000.0;

        $this->logger->debug('[TTN Uplink] Cálculo de peso neto', [
            'grossWeightGrams' => $grossWeightGrams,
            'tareGrams' => $tareGrams,
            'newWeightGrams' => $newWeightGrams,
            'newWeightKg' => $newWeightKg,
        ]);

        // Buscar el último WeightsLog de esta báscula
        $weightsLogRepo = $entityManager->getRepository(WeightsLog::class);
        $lastLog = $weightsLogRepo->findOneBy(
            ['scale' => $scale],
            ['date' => 'DESC']
        );

        // Si no hay registros previos, crear el primero
        if (!$lastLog) {
            $this->logger->info('[TTN Uplink] Primera lectura, creando registro inicial', [
                'newWeightGrams' => $newWeightGrams,
            ]);

            $weightLog = $this->weightsLogService->createLog(
                $entityManager,
                $scale,
                $newWeightKg,
                $newWeightGrams,
                $request->getVoltage(),
                $percentage
            );

            $this->logger->info('[TTN Uplink] Registro inicial creado', [
                'weightsLogId' => $weightLog->getId(),
            ]);

            return;
        }

        // Obtener último peso guardado
        $lastWeightGrams = $lastLog->getWeightGrams();
        $lastRealWeightKg = $lastLog->getRealWeight();
        $variationGrams = abs($newWeightGrams - $lastWeightGrams);

        $this->logger->debug('[TTN Uplink] Comparación de pesos', [
            'lastWeightGrams' => $lastWeightGrams,
            'lastRealWeightKg' => $lastRealWeightKg,
            'newWeightGrams' => $newWeightGrams,
            'variationGrams' => $variationGrams,
            'weightRange' => $weightRange,
        ]);

        // ============================================================
        // DECISIÓN: UPDATE vs INSERT
        // ============================================================

        if ($variationGrams < $weightRange) {
            // VARIACIÓN PEQUEÑA → ACTUALIZAR registro existente
            $this->logger->info('[TTN Uplink] Variación menor que umbral, ACTUALIZANDO registro existente', [
                'variationGrams' => $variationGrams,
                'weightRange' => $weightRange,
                'weightsLogId' => $lastLog->getId(),
                'lastWeightGrams' => $lastWeightGrams,
                'newWeightGrams' => $newWeightGrams,
            ]);

            $this->weightsLogService->updateMinorVariationLog(
                $lastLog,
                $newWeightGrams,
                $request->getVoltage(),
                $percentage,
                $entityManager
            );

            $this->logger->info('[TTN Uplink] Registro actualizado', [
                'weightsLogId' => $lastLog->getId(),
                'updatedWeightGrams' => $newWeightGrams,
                'realWeight' => $lastLog->getRealWeight(),
            ]);

            // NO ejecutar notificaciones (no hay cambio real de stock)
            return;
        }

        // ============================================================
        // VARIACIÓN SIGNIFICATIVA → CREAR nuevo registro
        // ============================================================

        // Calcular si es AUMENTO o DISMINUCIÓN (con signo)
        $weightDelta = $newWeightGrams - $lastWeightGrams;
        $weightDeltaKg = $weightDelta / 1000.0;

        // Calcular nuevo real_weight SUMANDO la diferencia
        $newRealWeightKg = $lastRealWeightKg + $weightDeltaKg;

        $this->logger->info('[TTN Uplink] Variación SIGNIFICATIVA detectada, creando nuevo registro', [
            'lastWeightGrams' => $lastWeightGrams,
            'newWeightGrams' => $newWeightGrams,
            'weightDelta' => $weightDelta,
            'weightDeltaKg' => $weightDeltaKg,
            'lastRealWeightKg' => $lastRealWeightKg,
            'newRealWeightKg' => $newRealWeightKg,
            'variationGrams' => $variationGrams,
            'weightRange' => $weightRange,
        ]);

        $variationKg = abs($weightDeltaKg);

        // ============================================================
        // NOTIFICACIONES (solo si hay cambio significativo)
        // ============================================================

        $now = new \DateTimeImmutable();
        $isHoliday = $this->businessHoursChecker->isHoliday($entityManager, $now);
        $isWithinBusinessHours = $this->businessHoursChecker->isWithinBusinessHours($entityManager, $now);
        $mainClient = null;

        $shouldNotifyForHoliday = $isHoliday && $notificationSettings['holidays'];
        $shouldNotifyOutOfHours = !$isWithinBusinessHours && $notificationSettings['out_of_hours'];

        if ($shouldNotifyForHoliday || $shouldNotifyOutOfHours) {
            $this->logger->info('[TTN Uplink] Variación detectada fuera de horario o en día festivo.', [
                'isHoliday' => $isHoliday,
                'isWithinBusinessHours' => $isWithinBusinessHours,
            ]);

            $mainClient = $this->notificationService->findMainClient($uuidClient);

            if (!$mainClient) {
                $this->logger->error('[TTN Uplink] CLIENT_NOT_FOUND para alerta de variación de peso.', [
                    'uuidClient' => $uuidClient,
                ]);
            } else {
                $alarmTypeId = $isHoliday ? 3 : 2;

                $this->logger->info('[TTN Uplink] Sending weight variation alert.', [
                    'alarmTypeId' => $alarmTypeId,
                ]);

                $this->notificationService->notifyWeightVariation(
                    $entityManager,
                    $uuidClient,
                    $mainClient,
                    (int) $product->getId(),
                    $product->getName(),
                    (int) $scale->getId(),
                    $deviceId,
                    (float) $lastRealWeightKg,
                    (float) $newRealWeightKg,
                    (float) $variationKg,
                    (float) $weightRange,
                    $nameUnit ?? 'Kg',
                    $now,
                    $isHoliday,
                    !$isWithinBusinessHours,
                    $alarmTypeId,
                    $product->getConversionFactor()
                );
            }
        } elseif ($isHoliday || !$isWithinBusinessHours) {
            $this->logger->info('[TTN Uplink] Alerta de variación omitida por configuración del cliente.', [
                'isHoliday' => $isHoliday,
                'isWithinBusinessHours' => $isWithinBusinessHours,
            ]);
        }

        // ============================================================
        // CREAR NUEVO REGISTRO EN BD
        // ============================================================

        $weightLog = $this->weightsLogService->createLog(
            $entityManager,
            $scale,
            $newRealWeightKg,
            $newWeightGrams,
            $request->getVoltage(),
            $percentage
        );

        $this->logger->info('[TTN Uplink] Nuevo registro creado', [
            'weightsLogId' => $weightLog->getId(),
            'weight_grams' => $newWeightGrams,
            'real_weight_kg' => $newRealWeightKg,
        ]);

        // ============================================================
        // NOTIFICACIÓN DE STOCK MÍNIMO
        // ============================================================

        $minimumStockInKg = $product->getMinimumStockInKg();
        if (null !== $minimumStockInKg && $newRealWeightKg <= $minimumStockInKg) {
            $this->logger->info('[TTN Uplink] Peso por debajo del stock mínimo', [
                'currentWeight' => $newRealWeightKg,
                'minimumStockInKg' => $minimumStockInKg,
                'minimumStockInUnits' => $product->getStock(),
            ]);

            $mainClient = $mainClient ?? $this->notificationService->findMainClient($uuidClient);
            if (!$mainClient) {
                $this->logger->error('[TTN Uplink] CLIENT_NOT_FOUND para notificación de stock.', [
                    'uuidClient' => $uuidClient,
                ]);
                return;
            }

            $this->logger->info('[TTN Uplink] Sending minimum stock alert.', [
                'alarmTypeId' => 1,
            ]);

            $this->notificationService->notifyMinimumStock(
                $entityManager,
                $uuidClient,
                $mainClient,
                (int) $product->getId(),
                $product->getName(),
                (int) $scale->getId(),
                $deviceId,
                (float) $newRealWeightKg,
                (float) $product->getStock(),
                (float) $weightRange,
                $nameUnit,
                $product->getConversionFactor()
            );
        }
    }
}