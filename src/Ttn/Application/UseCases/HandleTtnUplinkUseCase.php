<?php

namespace App\Ttn\Application\UseCases;

use App\Entity\Client\BusinessHour;
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
        $devEui = $request->getDevEui(); // o deviceId
        $this->logger->info('[TTN Uplink] Iniciando execute.', [
            'devEui' => $devEui,
            'deviceId' => $request->getDeviceId(),
            'voltage' => $request->getVoltage(),
            'weight' => $request->getWeight(),
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

        $uuidClient = $ttnDevice->getEndDeviceName(); // o si guardas en otra columna
        $this->logger->debug('[TTN Uplink] uuidClient obtenido', ['uuidClient' => $uuidClient]);

        // 2) Conectarte a la BBDD del cliente
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);

        $scaleRepository = $entityManager->getRepository(\App\Entity\Client\Scales::class);
        $scale = $scaleRepository->findOneBy(['end_device_id' => $deviceId]);
        if (!$scale) {
            $this->logger->error('SCALE_NOT_FOUND', ['deviceId' => $deviceId]);
            throw new \RuntimeException('SCALE_NOT_FOUND');
        }
        //porcentaje de las pilas
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
        // Esta línea fuerza a Doctrine a cargar todas las propiedades de $product y
        // lo convierte en un objeto real en lugar de un lazy ghost:
        $this->logger->debug('[TTN Uplink] Forzamos la inicialización de product');
        $entityManager->initializeObject($product);

        $weightRange = $product->getWeightRange() ?? 0.0;
        $this->logger->debug('[TTN Uplink] Obtenido weightRange del product', ['weightRange' => $weightRange]);

        $mainNameUnit = $product->getMainUnit();
        if (1 == $mainNameUnit) {
            $nameUnit = $product->getNameUnit1();
        } elseif (2 == $mainNameUnit) {
            $nameUnit = $product->getNameUnit2();
        } else {
            $nameUnit = 'Kg';
        }

        // Buscar el último WeightsLog de esta báscula, ordenado por fecha desc
        $weightsLogRepo = $entityManager->getRepository(WeightsLog::class);
        $lastLog = $weightsLogRepo->findOneBy(
            ['scale' => $scale],       // same scale
            ['date' => 'DESC']         // order by date desc
        );

        $previousWeight = $lastLog ? $lastLog->getRealWeight() : 0.0; // si no existe, p.ej. 0.0
        $newWeight = $request->getWeight();
        $variation = abs($newWeight - $previousWeight);
        if ($variation < $weightRange) {
            // Variación por debajo del umbral => descartar
            $this->logger->info('[TTN Uplink] Variación de peso menor que el rango, se descarta.', [
                'variation' => $variation,
                'weightRange' => $weightRange,
            ]);

            return;  // o "return" si no quieres guardar
        }

        $now = new \DateTimeImmutable();
        $isHoliday = $this->isHoliday($entityManager, $now);
        $isWithinBusinessHours = $this->isWithinBusinessHours($entityManager, $now);
        $mainClient = null;

        if ($isHoliday || !$isWithinBusinessHours) {
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
                $notification = new WeightVariationAlertNotification(
                    $uuidClient,
                    $mainClient->getClientName(),
                    $mainClient->getCompanyEmail(),
                    (int) $product->getId(),
                    $product->getName(),
                    (int) $scale->getId(),
                    $deviceId,
                    (float) $previousWeight,
                    (float) $newWeight,
                    (float) $variation,
                    (float) $weightRange,
                    $nameUnit ?? 'Kg',
                    $now,
                    $isHoliday,
                    !$isWithinBusinessHours
                );

                $this->weightVariationNotifier->notify($notification);
            }
        }

        // 3) Insertar en la tabla la “medición”

        $weightLog = new WeightsLog();
        $weightLog->setScale($scale);

        $weightLog->setProduct($scale->getProduct());
        $weightLog->setDate(new \DateTime());
        $weightLog->setRealWeight($request->getWeight());
        $weightLog->setAdjustWeight($request->getWeight());
        $weightLog->setChargePercentage(0.0);
        $weightLog->setVoltage($request->getVoltage());
        $weightLog->setChargePercentage($percentage);

        $entityManager->persist($weightLog);

        $entityManager->flush();
        $this->logger->info('[TTN Uplink] WeightsLog insertado correctamente', [
            'weightsLogId' => $weightLog->getId(),
            'scaleId' => $scale->getId(),
            'productId' => $product->getId(),
        ]);

        $minimumStock = $product->getStock();
        if (null !== $minimumStock && $newWeight <= $minimumStock) {
            $this->logger->info('[TTN Uplink] Peso por debajo del stock mínimo, preparando notificación.', [
                'currentWeight' => $newWeight,
                'minimumStock' => $minimumStock,
                'productId' => $product->getId(),
            ]);

            /** @var MainClient|null $mainClient */
            $mainClient = $mainClient ?? $this->findMainClient($uuidClient);
            if (!$mainClient) {
                $this->logger->error('[TTN Uplink] CLIENT_NOT_FOUND para notificación de stock.', [
                    'uuidClient' => $uuidClient,
                ]);

                return;
            }

            $notification = new MinimumStockNotification(
                $uuidClient,
                $mainClient->getClientName(),
                $mainClient->getCompanyEmail(),
                (int) $product->getId(),
                $product->getName(),
                (int) $scale->getId(),
                $deviceId,
                (float) $newWeight,
                (float) $minimumStock,
                (float) $weightRange,
                $nameUnit
            );

            $this->minimumStockNotifier->notify($notification);
        }

        //ahora guardar en la tabla de sacles la fecha del ultimo envío y el porcentaje de carga
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
}
