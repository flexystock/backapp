<?php

namespace App\Ttn\Application\UseCases;

use App\Ttn\Application\DTO\UnassignTtnDeviceRequest;
use App\Ttn\Application\DTO\UnassignTtnDeviceResponse;
use App\Ttn\Application\InputPorts\UnassignTtnDeviceUseCaseInterface;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;

class UnassignTtnDeviceUseCase implements UnassignTtnDeviceUseCaseInterface
{
    private TtnServiceInterface $ttnService;
    private PoolTtnDeviceRepositoryInterface $deviceRepository;

    public function __construct(TtnServiceInterface $ttnService, PoolTtnDeviceRepositoryInterface $deviceRepository)
    {
        $this->ttnService = $ttnService;
        $this->deviceRepository = $deviceRepository;
    }

    public function execute(UnassignTtnDeviceRequest $request): UnassignTtnDeviceResponse
    {
        try {
            // 1) Llamar a TTN para dejar "name = free"
            $this->ttnService->unassignDevice($request->getEndDeviceId());

            // 2) Buscar el dispositivo en la BBDD
            $ttnDevice = $this->deviceRepository->findOneBy($request->getEndDeviceId());
            if (!$ttnDevice) {
                return new UnassignTtnDeviceResponse(false, 'Device not found in DB');
            }

            // 3) Actualizar sus campos
            $ttnDevice->setAvailable(true);
            $ttnDevice->setEndDeviceName('free');
            $ttnDevice->setUuidUserModification($request->getUuidUserModification());
            $ttnDevice->setDatehourModification($request->getDatehourModification());

            // 4) Guardar en BBDD
            //    asumiendo que tu repositorio tenga algo como "save($device)"
            //    o uses el entityManager en el repositorio:
            // $this->deviceRepository->save($ttnDevice);

            // O si tu repositorio no tiene "save",
            // podrías exponer el entityManager->flush() allí. Ejemplo:
            $this->deviceRepository->update($ttnDevice);

            return new UnassignTtnDeviceResponse(true);
        } catch (\Exception $e) {
            // Maneja la excepción (API TTN o DB) y retorna un error
            return new UnassignTtnDeviceResponse(false, $e->getMessage());
        }
    }
}
