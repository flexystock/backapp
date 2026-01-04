<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\UseCases;

use App\ControlPanel\Ttn\Application\DTO\AssignTtnDeviceToClientRequest;
use App\ControlPanel\Ttn\Application\DTO\AssignTtnDeviceToClientResponse;
use App\ControlPanel\Ttn\Application\InputPorts\AssignTtnDeviceToClientUseCaseInterface;
use App\ControlPanel\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Entity\Client\PoolScale;
use App\Infrastructure\Services\ClientConnectionManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class AssignTtnDeviceToClientUseCase implements AssignTtnDeviceToClientUseCaseInterface
{
    private LoggerInterface $logger;
    private PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository;
    private ClientConnectionManager $connectionManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        LoggerInterface $logger,
        PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository,
        ClientConnectionManager $connectionManager,
        EntityManagerInterface $entityManager
    ) {
        $this->logger = $logger;
        $this->poolTtnDeviceRepository = $poolTtnDeviceRepository;
        $this->connectionManager = $connectionManager;
        $this->entityManager = $entityManager;
    }

    public function execute(AssignTtnDeviceToClientRequest $request): AssignTtnDeviceToClientResponse
    {
        $endDeviceId = $request->getEndDeviceId();
        $uuidClient = $request->getUuidClient();

        $this->logger->info("Executing AssignTtnDeviceToClientUseCase for device: {$endDeviceId} and client: {$uuidClient}");

        try {
            // 1. Find the device in pool_ttn_device
            $ttnDevice = $this->poolTtnDeviceRepository->findOneByEndDeviceId($endDeviceId);
            
            if (!$ttnDevice) {
                return new AssignTtnDeviceToClientResponse(
                    false,
                    'Device not found in pool_ttn_device',
                    404
                );
            }

            // 2. Update the end_device_name with the client UUID
            $ttnDevice->setEndDeviceName($uuidClient);
            $ttnDevice->setUuidUserModification('system'); // or get from authenticated user
            $ttnDevice->setDatehourModification(new \DateTimeImmutable());

            // 3. Save the updated device in pool_ttn_device (main DB)
            $this->poolTtnDeviceRepository->save($ttnDevice);

            // 4. Create a new record in pool_scales (client DB)
            $this->entityManager->beginTransaction();
            try {
                // Get the client's entity manager
                $clientEntityManager = $this->connectionManager->getEntityManager($uuidClient);

                // Create the PoolScale entity
                $poolScale = new PoolScale();
                $poolScale->setUuid(Uuid::v4()->toRfc4122());
                $poolScale->setEndDeviceId($endDeviceId);
                $poolScale->setAvailable(true);
                $poolScale->setEndDeviceName($uuidClient);
                $poolScale->setAppEUI($ttnDevice->getAppEUI());
                $poolScale->setDevEUI($ttnDevice->getDevEUI());
                $poolScale->setAppKey($ttnDevice->getAppKey());
                $poolScale->setDatehourCreation(new \DateTime());
                $poolScale->setUuidUserCreation('system'); // or get from authenticated user

                // Save to client's database
                $clientEntityManager->persist($poolScale);
                $clientEntityManager->flush();

                $this->entityManager->commit();

                $this->logger->info("Successfully assigned device {$endDeviceId} to client {$uuidClient}");

                return new AssignTtnDeviceToClientResponse(
                    true,
                    'Device assigned to client successfully',
                    200
                );
            } catch (\Exception $e) {
                $this->entityManager->rollback();
                $this->logger->error("Failed to create record in pool_scales: {$e->getMessage()}");

                return new AssignTtnDeviceToClientResponse(
                    false,
                    'Failed to create record in client database: ' . $e->getMessage(),
                    500
                );
            }
        } catch (\Exception $e) {
            $this->logger->error("Exception while assigning device to client: {$e->getMessage()}");

            return new AssignTtnDeviceToClientResponse(
                false,
                'Failed to assign device to client: ' . $e->getMessage(),
                500
            );
        }
    }
}
