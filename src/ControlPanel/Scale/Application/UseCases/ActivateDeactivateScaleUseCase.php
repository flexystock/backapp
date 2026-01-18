<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\UseCases;

use App\ControlPanel\Scale\Application\DTO\ActivateDeactivateScaleRequest;
use App\ControlPanel\Scale\Application\DTO\ActivateDeactivateScaleResponse;
use App\ControlPanel\Scale\Application\InputPorts\ActivateDeactivateScaleUseCaseInterface;
use App\ControlPanel\Scale\Application\OutputPorts\ClientRepositoryInterface;
use App\ControlPanel\Scale\Application\OutputPorts\ClientScalesRepositoryInterface;
use Psr\Log\LoggerInterface;

class ActivateDeactivateScaleUseCase implements ActivateDeactivateScaleUseCaseInterface
{
    private LoggerInterface $logger;
    private ClientRepositoryInterface $clientRepository;
    private ClientScalesRepositoryInterface $clientScalesRepository;

    public function __construct(
        LoggerInterface $logger,
        ClientRepositoryInterface $clientRepository,
        ClientScalesRepositoryInterface $clientScalesRepository
    ) {
        $this->logger = $logger;
        $this->clientRepository = $clientRepository;
        $this->clientScalesRepository = $clientScalesRepository;
    }

    public function execute(ActivateDeactivateScaleRequest $request): ActivateDeactivateScaleResponse
    {
        $clientName = $request->getClientName();
        $endDeviceId = $request->getEndDeviceId();
        $active = $request->isActive();

        $this->logger->info("Executing ActivateDeactivateScaleUseCase for client: {$clientName}, scale: {$endDeviceId}, active: " . ($active ? 'true' : 'false'));

        // Find client by name
        $client = $this->clientRepository->findOneByName($clientName);

        if (!$client) {
            $this->logger->warning("Client not found with name: {$clientName}");

            return new ActivateDeactivateScaleResponse(null, 'Client not found', 404);
        }

        $clientUuid = $client->getUuidClient();

        // Update the active status in the client's database
        $success = $this->clientScalesRepository->updateActiveStatus($clientUuid, $endDeviceId, $active);

        if (!$success) {
            return new ActivateDeactivateScaleResponse(null, 'Failed to update scale status. Scale may not exist.', 404);
        }

        $statusMessage = $active ? 'activated' : 'deactivated';
        $message = "Scale {$endDeviceId} successfully {$statusMessage}";

        return new ActivateDeactivateScaleResponse($message, null, 200);
    }
}
