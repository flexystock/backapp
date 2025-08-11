<?php

namespace App\Ttn\Application\UseCases;

use App\Entity\Client\PoolScale;
use App\Entity\Main\PoolTtnDevice;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\OutputPorts\PoolScalesRepositoryInterface;
use App\Ttn\Application\DTO\RegisterTtnDeviceRequest;
use App\Ttn\Application\DTO\RegisterTtnDeviceResponse;
use App\Ttn\Application\InputPorts\RegisterTtnDeviceUseCaseInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;
use Symfony\Component\Uid\Uuid;

class RegisterTtnDeviceUseCase implements RegisterTtnDeviceUseCaseInterface
{
    private TtnServiceInterface $ttnService;
    private PoolTtnDeviceRepositoryInterface $deviceRepository;
    private PoolScalesRepositoryInterface $poolScaleRepository;
    private ClientConnectionManager $connectionManager;

    public function __construct(
        TtnServiceInterface $ttnService,
        PoolTtnDeviceRepositoryInterface $deviceRepository,
        PoolScalesRepositoryInterface $poolScaleRepository,
        ClientConnectionManager $connectionManager
    ) {
        $this->ttnService = $ttnService;
        $this->deviceRepository = $deviceRepository;
        $this->poolScaleRepository = $poolScaleRepository;
        $this->connectionManager = $connectionManager;
    }

    public function execute(RegisterTtnDeviceRequest $request): RegisterTtnDeviceResponse
    {
        try {
            /// 1. Buscar el último dispositivo para obtener el siguiente id correlativo
            $lastDevice = $this->deviceRepository->findLastDevice();

            if ($lastDevice) {
                $lastDeviceId = $lastDevice->getEndDeviceId(); // Ej: "fs-0007"
                // Extraer el número del id
                $matches = [];
                preg_match('/fs-(\d+)/', $lastDeviceId, $matches);
                $numericPart = isset($matches[1]) ? (int) $matches[1] : 0;
                $nextNumber = $numericPart + 1;
                $nextDeviceId = sprintf('fs-%04d', $nextNumber); // Ej: "fs-0008"
            } else {
                $nextDeviceId = 'fs-0001';
            }
            // 1) Generar EUI/AppKey si faltan
            $devEui = $request->getDevEui() ?? $this->generateEui();
            $joinEui = $request->getJoinEui() ?? $this->generateEui();
            $appKey = $request->getAppKey() ?? $this->generateAppKey();

            // 2) Actualizar la request (opcional)
            //    - O crea un constructor nuevo para TTNService
            //    - O solo pasamos estos a la TtnService -> registerDevice(...)
            $dtoForTtn = new RegisterTtnDeviceRequest(
                $request->getUuidUser(),
                $request->getDatehourCreation(),
                $request->getUuidClient(),
                $devEui,
                $joinEui,
                $appKey,
                $nextDeviceId // <-- deviceId como último argumento
            );

            // 3) Llamar a TTN (si falla, lanza excepción)
            $this->ttnService->registerDevice($dtoForTtn);

            // 4) Crear la entidad local en BD
            $ttnDevice = new PoolTtnDevice();
            $ttnDevice->setAvailable(true);
            $ttnDevice->setEndDeviceId($nextDeviceId);
            $ttnDevice->setEndDeviceName($request->getUuidClient() ?? 'free');
            $ttnDevice->setAppEUI($devEui);
            $ttnDevice->setDevEUI($joinEui);
            $ttnDevice->setAppKey($appKey);
            $ttnDevice->setUuidUserCreation($request->getUuidUser());
            $ttnDevice->setDatehourCreation($request->getDatehourCreation());

            // 5) Guardar en BBDD
            $this->deviceRepository->save($ttnDevice);

            // 6) Si viene uuidClient, actualizar PoolScale
            if ($request->getUuidClient()) {
                $poolScale = new PoolScale();
                $poolScale->setUuid(Uuid::v4()->toRfc4122());
                $poolScale->setEndDeviceId($nextDeviceId);
                $poolScale->setAvailable(true);
                $poolScale->setEndDeviceName($request->getUuidClient());
                $poolScale->setAppEUI($devEui);
                $poolScale->setDevEUI($joinEui);
                $poolScale->setAppKey($appKey);
                $poolScale->setDatehourCreation($request->getDatehourCreation());
                $poolScale->setUuidUserCreation($request->getUuidUser());

                // Guardar PoolScale
                $emCliente = $this->connectionManager->getEntityManager($request->getUuidClient());
                $poolScalesRepo = new \App\Scales\Infrastructure\OutputAdapters\Repositories\PoolScalesRepository($emCliente);
                $poolScalesRepo->savePoolScale($poolScale);
            }

            return new RegisterTtnDeviceResponse(true);
        } catch (\Exception $e) {
            // Maneja la excepción (API TTN o DB) y retorna un error
            return new RegisterTtnDeviceResponse(false, $e->getMessage());
        }
    }

    private function generateEui(): string
    {
        // Genera un EUI de 8 bytes en hex
        return strtoupper(bin2hex(random_bytes(8)));
    }

    private function generateAppKey(): string
    {
        // Genera un AppKey de 16 bytes en hex
        return strtoupper(bin2hex(random_bytes(16)));
    }
}
