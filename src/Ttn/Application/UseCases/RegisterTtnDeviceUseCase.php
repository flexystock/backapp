<?php

namespace App\Ttn\Application\UseCases;

use App\Entity\Main\PoolTtnDevice;
use App\Entity\Client\PoolScale;
use App\Ttn\Application\DTO\RegisterTtnDeviceRequest;
use App\Ttn\Application\DTO\RegisterTtnDeviceResponse;
use App\Ttn\Application\InputPorts\RegisterTtnDeviceUseCaseInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;
use App\Scales\Application\OutputPorts\PoolScalesRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;

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
            // 1) Generar EUI/AppKey si faltan
            $devEui = $request->getDevEui() ?? $this->generateEui();
            $joinEui = $request->getJoinEui() ?? $this->generateEui();
            $appKey = $request->getAppKey() ?? $this->generateAppKey();

            // 2) Actualizar la request (opcional)
            //    - O crea un constructor nuevo para TTNService
            //    - O solo pasamos estos a la TtnService -> registerDevice(...)
            $dtoForTtn = new RegisterTtnDeviceRequest(
                $request->getDeviceId(),
                $request->getUuidUser(),
                $request->getDatehourCreation(),
                $request->getUuidClient(),
                $devEui,
                $joinEui,
                $appKey
            );

            // 3) Llamar a TTN (si falla, lanza excepción)
            $this->ttnService->registerDevice($dtoForTtn);

            // 4) Crear la entidad local en BD
            $ttnDevice = new PoolTtnDevice();
            $ttnDevice->setAvailable(true);
            $ttnDevice->setEndDeviceId($request->getDeviceId());
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
                $poolScale->setEndDeviceId($request->getDeviceId());
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
