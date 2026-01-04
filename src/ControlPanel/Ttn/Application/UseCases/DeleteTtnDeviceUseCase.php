<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\UseCases;

use App\ControlPanel\Ttn\Application\DTO\DeleteTtnDeviceRequest;
use App\ControlPanel\Ttn\Application\DTO\DeleteTtnDeviceResponse;
use App\ControlPanel\Ttn\Application\InputPorts\DeleteTtnDeviceUseCaseInterface;
use App\ControlPanel\Ttn\Application\OutputPorts\PoolScalesRepositoryInterface;
use App\ControlPanel\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Entity\Client\PoolScale;
use App\Entity\Client\Scales;
use App\Entity\Main\Client;
use App\Infrastructure\Services\ClientConnectionManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class DeleteTtnDeviceUseCase implements DeleteTtnDeviceUseCaseInterface
{
    private LoggerInterface $logger;
    private PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository;
    private PoolScalesRepositoryInterface $poolScalesRepository;
    private EntityManagerInterface $mainEntityManager;
    private ClientConnectionManager $clientConnectionManager;

    public function __construct(
        LoggerInterface $logger,
        PoolTtnDeviceRepositoryInterface $poolTtnDeviceRepository,
        PoolScalesRepositoryInterface $poolScalesRepository,
        EntityManagerInterface $entityManager,
        ClientConnectionManager $clientConnectionManager
    ) {
        $this->logger = $logger;
        $this->poolTtnDeviceRepository = $poolTtnDeviceRepository;
        $this->poolScalesRepository = $poolScalesRepository;
        $this->mainEntityManager = $entityManager;
        $this->clientConnectionManager = $clientConnectionManager;
    }

    public function execute(DeleteTtnDeviceRequest $request): DeleteTtnDeviceResponse
    {
        $endDeviceId = $request->getEndDeviceId();

        $this->logger->info("Executing DeleteTtnDeviceUseCase for device: {$endDeviceId}");

        // 1. Check if device exists in pool_ttn_device (Main DB)
        $poolDevice = $this->poolTtnDeviceRepository->findOneByEndDeviceId($endDeviceId);
        if (!$poolDevice) {
            return new DeleteTtnDeviceResponse(
                false,
                'Device not found in pool_ttn_device',
                404
            );
        }

        // 2. Get uuid_client from end_device_name
        $uuidClient = $poolDevice->getEndDeviceName();

        if (!$uuidClient || $uuidClient === 'free') {
            // Device already free or not assigned
            $this->logger->info("Device is already free or not assigned to any client");
            return new DeleteTtnDeviceResponse(
                false,
                'Device is already free or not assigned to any client',
                400
            );
        }

        // 3. Get client from Main DB
        $clientRepo = $this->mainEntityManager->getRepository(Client::class);
        $client = $clientRepo->findOneBy(['uuid_client' => $uuidClient]);

        if (!$client) {
            return new DeleteTtnDeviceResponse(
                false,
                'Client not found for uuid: ' . $uuidClient,
                404
            );
        }

        $clientName = $client->getDatabaseName();
        $this->logger->info("Device belongs to client: {$clientName} (UUID: {$uuidClient})");

        // 4. Connect to Client DB
        try {
            $clientEntityManager = $this->clientConnectionManager->getEntityManager($uuidClient);
        } catch (\Exception $e) {
            $this->logger->error("Failed to connect to client DB: {$e->getMessage()}");
            return new DeleteTtnDeviceResponse(
                false,
                'Failed to connect to client database',
                500
            );
        }

        // 5. Check if device has associated product in client's scales table
        $scalesRepo = $clientEntityManager->getRepository(Scales::class);
        $scale = $scalesRepo->findOneBy(['end_device_id' => $endDeviceId]);

        if ($scale && $scale->getProduct() !== null) {
            $this->logger->warning("Cannot free device {$endDeviceId}: associated with product");
            return new DeleteTtnDeviceResponse(
                false,
                'Cannot free device. It is associated with a product. Please disassociate it first.',
                400
            );
        }

        $this->logger->info("Device is not associated with any product, proceeding with freeing");

        // 6. Free device (update databases but keep in TTN)
        try {
            // First: Delete from pool_scales (Client DB)
            $poolScalesRepo = $clientEntityManager->getRepository(PoolScale::class);
            $poolScale = $poolScalesRepo->findOneBy(['end_device_id' => $endDeviceId]);

            if ($poolScale) {
                $this->logger->info("Found entry in pool_scales, deleting...");
                $clientEntityManager->remove($poolScale);
                $clientEntityManager->flush();
                $this->logger->info("Deleted from pool_scales (Client DB)");
            } else {
                $this->logger->info("No entry found in pool_scales for device {$endDeviceId}");
            }

            // Second: Update pool_ttn_device to mark as 'free' (Main DB)
            $poolDevice->setEndDeviceName('free');
            $poolDevice->setAvailable(true); // Mark as available
            $this->mainEntityManager->persist($poolDevice);
            $this->mainEntityManager->flush();
            $this->logger->info("Updated pool_ttn_device: set end_device_name='free' and available=true");

            $this->logger->info("Successfully freed device: {$endDeviceId}");

            return new DeleteTtnDeviceResponse(
                true,
                'Device freed successfully. It is now available for reassignment.',
                200
            );
        } catch (\Exception $e) {
            $this->logger->error("Failed to free device: {$e->getMessage()}");

            return new DeleteTtnDeviceResponse(
                false,
                'Failed to free device from database: ' . $e->getMessage(),
                500
            );
        }
    }
}