<?php

namespace App\Ttn\Application\UseCases;

use App\Entity\Client\BusinessHour;
use App\Entity\Client\ClientConfig;
use App\Entity\Client\Holiday;
use App\Entity\Client\WeightsLog;
use App\Entity\Main\Client as MainClient;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use App\Ttn\Application\DTO\MinimumStockNotification;
use App\Ttn\Application\DTO\WeightVariationAlertNotification;
use App\Ttn\Application\DTO\TtnUplinkRequest;
use App\Ttn\Application\InputPorts\HandleTtnUplinkUseCaseInterface;
use App\Ttn\Application\OutputPorts\MinimumStockNotificationInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Ttn\Application\OutputPorts\WeightVariationAlertNotifierInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class HandleTtnUplinkUseCase implements HandleTtnUplinkUseCaseInterface
{
    private PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository;
    private ClientConnectionManager $connectionManager;
    private ScalesRepositoryInterface $ScaleRepository;
    private LoggerInterface $logger;
    private EntityManagerInterface $mainEntityManager;
    private MinimumStockNotificationInterface $minimumStockNotifier;
    private WeightVariationAlertNotifierInterface $weightVariationNotifier;

    public function __construct(
        PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepo,
        ClientConnectionManager $connManager,
        ScalesRepositoryInterface $ScaleRepository,
        LoggerInterface $logger,
        EntityManagerInterface $mainEntityManager,
        MinimumStockNotificationInterface $minimumStockNotifier,
        WeightVariationAlertNotifierInterface $weightVariationNotifier
    ) {
        $this->poolTtnDeviceRepository = $poolTtnDeviceRepo;
        $this->connectionManager = $connManager;
        $this->ScaleRepository = $ScaleRepository;
        $this->logger = $logger;
        $this->mainEntityManager = $mainEntityManager;
        $this->minimumStockNotifier = $minimumStockNotifier;
        $this->weightVariationNotifier = $weightVariationNotifier;
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

        $notificationSettings = $this->getNotificationSettings($entityManager);
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

            $weightLog = new WeightsLog();
            $weightLog->setScale($scale);
            $weightLog->setProduct($scale->getProduct());
            $weightLog->setDate(new \DateTime());
            $weightLog->setRealWeight($newWeightKg);
            $weightLog->setWeightGrams($newWeightGrams);
            $weightLog->setAdjustWeight($newWeightKg);
            $weightLog->setVoltage($request->getVoltage());
            $weightLog->setChargePercentage($percentage);

            $entityManager->persist($weightLog);
            $entityManager->flush();

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

            // Actualizar el registro existente con el nuevo peso
            $lastLog->setWeightGrams($newWeightGrams);
            $lastLog->setDate(new \DateTime());
            $lastLog->setVoltage($request->getVoltage());
            $lastLog->setChargePercentage($percentage);

            // NO cambiar real_weight (sigue siendo el mismo número de tornillos)

            $entityManager->flush();

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
        $isHoliday = $this->isHoliday($entityManager, $now);
        $isWithinBusinessHours = $this->isWithinBusinessHours($entityManager, $now);
        $mainClient = null;

        $shouldNotifyForHoliday = $isHoliday && $notificationSettings['holidays'];
        $shouldNotifyOutOfHours = !$isWithinBusinessHours && $notificationSettings['out_of_hours'];

        if ($shouldNotifyForHoliday || $shouldNotifyOutOfHours) {
            $this->logger->info('[TTN Uplink] Variación detectada fuera de horario o en día festivo.', [
                'isHoliday' => $isHoliday,
                'isWithinBusinessHours' => $isWithinBusinessHours,
            ]);

            $mainClient = $this->findMainClient($uuidClient);

            if (!$mainClient) {
                $this->logger->error('[TTN Uplink] CLIENT_NOT_FOUND para alerta de variación de peso.', [
                    'uuidClient' => $uuidClient,
                ]);
            } else {
                $alarmTypeId = $isHoliday ? 3 : 2;
                $recipientEmails = $this->getRecipientEmailsForAlarmType($entityManager, $uuidClient, $alarmTypeId);

                $this->logger->info('[TTN Uplink] Retrieved recipients for weight variation alert.', [
                    'alarmTypeId' => $alarmTypeId,
                    'recipientCount' => count($recipientEmails),
                ]);

                $notification = new WeightVariationAlertNotification(
                    $uuidClient,
                    $mainClient->getClientName(),
                    $recipientEmails,
                    (int) $product->getId(),
                    $product->getName(),
                    (int) $scale->getId(),
                    $deviceId,
                    (float) $lastRealWeightKg,      // Peso anterior
                    (float) $newRealWeightKg,       // Peso nuevo CALCULADO
                    (float) $variationKg,
                    (float) $weightRange,
                    $nameUnit ?? 'Kg',
                    $now,
                    $isHoliday,
                    !$isWithinBusinessHours
                );

                $this->weightVariationNotifier->notify($notification);
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

        $weightLog = new WeightsLog();
        $weightLog->setScale($scale);
        $weightLog->setProduct($scale->getProduct());
        $weightLog->setDate(new \DateTime());
        $weightLog->setRealWeight($newRealWeightKg);        // ← PESO CALCULADO
        $weightLog->setWeightGrams($newWeightGrams);
        $weightLog->setAdjustWeight($newRealWeightKg);      // ← PESO CALCULADO
        $weightLog->setVoltage($request->getVoltage());
        $weightLog->setChargePercentage($percentage);

        $entityManager->persist($weightLog);
        $entityManager->flush();

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

            $mainClient = $mainClient ?? $this->findMainClient($uuidClient);
            if (!$mainClient) {
                $this->logger->error('[TTN Uplink] CLIENT_NOT_FOUND para notificación de stock.', [
                    'uuidClient' => $uuidClient,
                ]);
                return;
            }

            $recipientEmails = $this->getRecipientEmailsForAlarmType($entityManager, $uuidClient, 1);

            $this->logger->info('[TTN Uplink] Retrieved recipients for stock alert.', [
                'alarmTypeId' => 1,
                'recipientCount' => count($recipientEmails),
            ]);

            $notification = new MinimumStockNotification(
                $uuidClient,
                $mainClient->getClientName(),
                $recipientEmails,
                (int) $product->getId(),
                $product->getName(),
                (int) $scale->getId(),
                $deviceId,
                (float) $newRealWeightKg,           // ← PESO CALCULADO
                (float) $product->getStock(),       // ← Stock en unidades configuradas
                (float) $weightRange,
                $nameUnit,
                $product->getConversionFactor()     // ← Factor de conversión
            );

            $this->minimumStockNotifier->notify($notification);
        }
    }

    private function isHoliday(EntityManagerInterface $entityManager, \DateTimeImmutable $dateTime): bool
    {
        $holidayRepo = $entityManager->getRepository(Holiday::class);

        $count = $holidayRepo->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.holidayDate = :date')
            ->setParameter('date', $dateTime->setTime(0, 0), Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();

        return ((int) $count) > 0;
    }

    private function isWithinBusinessHours(EntityManagerInterface $entityManager, \DateTimeImmutable $dateTime): bool
    {
        $dayOfWeek = (int) $dateTime->format('N');
        $businessHours = $entityManager->getRepository(BusinessHour::class)->findBy([
            'dayOfWeek' => $dayOfWeek,
        ]);

        foreach ($businessHours as $businessHour) {
            if ($businessHour->coversDateTime($dateTime)) {
                return true;
            }
        }

        return false;
    }

    private function findMainClient(string $uuidClient): ?MainClient
    {
        return $this->mainEntityManager->getRepository(MainClient::class)->find($uuidClient);
    }

    /**
     * @return array{out_of_hours: bool, holidays: bool, battery_shelve: bool}
     */
    private function getNotificationSettings(EntityManagerInterface $entityManager): array
    {
        $clientConfig = $entityManager->getRepository(ClientConfig::class)->findOneBy([]);

        if (!$clientConfig instanceof ClientConfig) {
            return [
                'out_of_hours' => true,
                'holidays' => true,
                'battery_shelve' => true,
            ];
        }

        return [
            'out_of_hours' => $clientConfig->isCheckOutOfHours(),
            'holidays' => $clientConfig->isCheckHolidays(),
            'battery_shelve' => $clientConfig->isCheckBatteryShelve(),
        ];
    }

    private function normalizeErrorCode(int|string|null $errorCode): ?int
    {
        if (null === $errorCode) {
            return null;
        }

        if (is_int($errorCode)) {
            return 0 !== $errorCode ? $errorCode : null;
        }

        return is_numeric($errorCode) ? ((int) $errorCode ?: null) : null;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param string $uuidClient
     * @param int $alarmTypeId
     * @return string[]
     */
    private function getRecipientEmailsForAlarmType(
        EntityManagerInterface $entityManager,
        string $uuidClient,
        int $alarmTypeId
    ): array {
        $query = $entityManager->createQuery(
            'SELECT atr.email FROM App\Entity\Client\AlarmTypeRecipient atr 
             JOIN atr.alarmType at
             WHERE atr.uuid_client = :uuidClient AND at.id = :alarmTypeId'
        );
        $query->setParameter('uuidClient', $uuidClient);
        $query->setParameter('alarmTypeId', $alarmTypeId);

        $result = $query->getResult();

        return array_map(fn($row) => $row['email'], $result);
    }
}