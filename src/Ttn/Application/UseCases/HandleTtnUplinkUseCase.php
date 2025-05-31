<?php

namespace App\Ttn\Application\UseCases;

use App\Entity\Client\WeightsLog;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use App\Ttn\Application\DTO\TtnUplinkRequest;
use App\Ttn\Application\InputPorts\HandleTtnUplinkUseCaseInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use Psr\Log\LoggerInterface;

class HandleTtnUplinkUseCase implements HandleTtnUplinkUseCaseInterface
{
    private PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository;
    private ClientConnectionManager $connectionManager;
    private ScalesRepositoryInterface $ScaleRepository;
    private LoggerInterface $logger;

    public function __construct(
        PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepo,
        ClientConnectionManager $connManager,
        ScalesRepositoryInterface $ScaleRepository,
        LoggerInterface $logger
    ) {
        $this->poolTtnDeviceRepository = $poolTtnDeviceRepo;
        $this->connectionManager = $connManager;
        $this->ScaleRepository = $ScaleRepository;
        $this->logger = $logger;
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
            'percentage' => $percentage
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

        $weightRange = $product->getWeightRange();
        $this->logger->debug('[TTN Uplink] Obtenido weightRange del product', ['weightRange' => $weightRange]);

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
            'scaleId'      => $scale->getId(),
            'productId'    => $product->getId(),
        ]);

        //ahora guardar en la tabla de sacles la fecha del ultimo envío y el porcentaje de carga

    }
}
