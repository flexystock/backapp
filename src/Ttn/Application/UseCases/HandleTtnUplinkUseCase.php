<?php

namespace App\Ttn\Application\UseCases;

use App\Entity\Client\WeightsLog;
use App\Product\Infrastructure\OutputAdapters\Services\ClientConnectionManager;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use App\Ttn\Application\DTO\TtnUplinkRequest;
use App\Ttn\Application\InputPorts\HandleTtnUplinkUseCaseInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;

class HandleTtnUplinkUseCase implements HandleTtnUplinkUseCaseInterface
{
    private PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository;
    private ClientConnectionManager $connectionManager;
    private ScalesRepositoryInterface $ScaleRepository;

    public function __construct(
        PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepo,
        ClientConnectionManager $connManager,
        ScalesRepositoryInterface $ScaleRepository,
    ) {
        $this->poolTtnDeviceRepository = $poolTtnDeviceRepo;
        $this->connectionManager = $connManager;
        $this->ScaleRepository = $ScaleRepository;
    }

    public function execute(TtnUplinkRequest $request): void
    {
        $devEui = $request->getDevEui(); // o deviceId
        if (!$devEui) {
            // Lanza excepción o ignora
            throw new \RuntimeException('DEV_EUI_MISSING');
        }
        $deviceId = $request->getDeviceId();
        if (!$deviceId) {
            // Lanza excepción o ignora
            throw new \RuntimeException('DEVICE_ID_MISSING');
        }
        // 1) Buscar en pool_ttn_device
        $ttnDevice = $this->poolTtnDeviceRepository->findOneBy($deviceId);
        if (!$ttnDevice) {
            throw new \RuntimeException('DEVICE_NOT_FOUND');
        }

        $uuidClient = $ttnDevice->getEndDeviceName(); // o si guardas en otra columna
        // 2) Conectarte a la BBDD del cliente
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);

        $scaleRepository = $entityManager->getRepository(\App\Entity\Client\Scales::class);
        $scale = $scaleRepository->findOneBy(['end_device_id' => $deviceId]);
        if (!$scale) {
            throw new \RuntimeException('SCALE_NOT_FOUND');
        }
        //porcentaje de las pilas
        $percentage = max(0, min(100, ($request->getVoltage() - 3.2) / (3.6 - 3.2) * 100));
        $scale->setVoltagePercentage($percentage);
        $scale->setLastSend(new \DateTime());
        $entityManager->persist($scale);
        $entityManager->flush();

        $product = $scale->getProduct();
        if (!$product) {
            throw new \RuntimeException('PRODUCT_NOT_FOUND');
        }
        $weightRange = $product->getWeightRange();

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

        //ahora guardar en la tabla de sacles la fecha del ultimo envío y el porcentaje de carga

    }
}
