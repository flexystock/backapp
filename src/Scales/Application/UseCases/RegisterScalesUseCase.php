<?php

namespace App\Scales\Application\UseCases;

use App\Entity\Client\Scales;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\RegisterScalesRequest;
use App\Scales\Application\DTO\RegisterScalesResponse;
use App\Scales\Application\InputPorts\RegisterScalesUseCaseInterface;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use App\Scales\Infrastructure\OutputAdapters\Repositories\ScalesRepository;

class RegisterScalesUseCase implements RegisterScalesUseCaseInterface
{
    private ScalesRepositoryInterface $scalesRepository;
    private ClientConnectionManager $connectionManager;

    public function __construct(
        ScalesRepositoryInterface $scalesRepository,
        ClientConnectionManager $connectionManager,
    ) {
        $this->scalesRepository = $scalesRepository;
        $this->connectionManager = $connectionManager;
    }

    public function execute(RegisterScalesRequest $request): RegisterScalesResponse
    {
        try {
            // 1. Seleccionar la “conexión del cliente”
            //    Esto depende de cómo manejas la multibdd.
            //    Podrías tener un "ConnectionManager" o algo similar.
            //    scalesRepository -> setClient($request->getUuidClient()) ... etc.
            // $this->scalesRepository->selectClientConnection($request->getUuidClient());
            $em = $this->connectionManager->getEntityManager($request->getUuidClient());
            // Crear el repositorio
            $scalesRepository = new ScalesRepository($em);

            // Crear la entidad Product (en src/Entity/Client/Product.php)
            $scales = new Scales();
            $scales->setUuid($this->generateUuid()); // O tu propia lógica
            $scales->setEndDeviceId($request->getEndDeviceId());
            $scales->setVoltageMin($request->getVoltageMin() ?? 3.2);

            $userCreation = $request->getUuidUserCreation() ?: 'system';
            $scales->setUuidUserCreation($userCreation);
            $scales->setDatehourCreation(new \DateTime());

            // 3. Persistir
            $scalesRepository->save($scales);

            return new RegisterScalesResponse(true);
        } catch (\Exception $e) {
            return new RegisterScalesResponse(false, $e->getMessage());
        }
    }

    private function generateUuid(): string
    {
        // O usa ramsey/uuid. Esto es un stub
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            rand(0, 0xFFFF), rand(0, 0xFFFF),
            rand(0, 0xFFFF),
            rand(0, 0x0FFF) | 0x4000,
            rand(0, 0x3FFF) | 0x8000,
            rand(0, 0xFFFF), rand(0, 0xFFFF), rand(0, 0xFFFF)
        );
    }
}
