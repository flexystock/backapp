<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\UseCases;

use App\ControlPanel\Scale\Application\DTO\GetScaleInfoRequest;
use App\ControlPanel\Scale\Application\DTO\GetScaleInfoResponse;
use App\ControlPanel\Scale\Application\InputPorts\GetScaleInfoUseCaseInterface;
use App\ControlPanel\Scale\Application\OutputPorts\ClientRepositoryInterface;
use App\ControlPanel\Scale\Application\OutputPorts\ScaleRepositoryInterface;
use App\Entity\Main\PoolTtnDevice;
use Psr\Log\LoggerInterface;

class GetScaleInfoUseCase implements GetScaleInfoUseCaseInterface
{
    private LoggerInterface $logger;
    private ScaleRepositoryInterface $scaleRepository;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        LoggerInterface $logger,
        ScaleRepositoryInterface $scaleRepository,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->logger = $logger;
        $this->scaleRepository = $scaleRepository;
        $this->clientRepository = $clientRepository;
    }

    public function execute(GetScaleInfoRequest $request): GetScaleInfoResponse
    {
        $endDeviceId = $request->getEndDeviceId();

        if ($endDeviceId) {
            // Get specific scale
            $this->logger->info("Executing ControlPanel GetScaleInfoUseCase for scale: {$endDeviceId}");
            $scale = $this->scaleRepository->findOneByEndDeviceId($endDeviceId);

            if (!$scale) {
                return new GetScaleInfoResponse(null, 'Scale not found', 404);
            }

            // Load client for single scale
            $endDeviceName = $scale->getEndDeviceName();
            $clients = [];
            if ($endDeviceName) {
                $clients = $this->clientRepository->findByUuids([$endDeviceName]);
            }

            $scaleInfo = $this->mapScaleToArray($scale, $clients);

            return new GetScaleInfoResponse([$scaleInfo], null, 200);
        } else {
            // Get all scales
            $this->logger->info('Executing ControlPanel GetScaleInfoUseCase for all scales');
            $scales = $this->scaleRepository->findAll();

            // Batch load clients to avoid N+1 query problem
            $clientUuids = [];
            foreach ($scales as $scale) {
                $endDeviceName = $scale->getEndDeviceName();
                if ($endDeviceName) {
                    $clientUuids[] = $endDeviceName;
                }
            }
            
            $clients = $this->clientRepository->findByUuids(array_unique($clientUuids));

            // Map scales to array
            $scalesInfo = [];
            foreach ($scales as $scale) {
                $scalesInfo[] = $this->mapScaleToArray($scale, $clients);
            }

            return new GetScaleInfoResponse($scalesInfo, null, 200);
        }
    }

    private function mapScaleToArray(PoolTtnDevice $scale, array $clients = []): array
    {
        $clientName = null;
        $endDeviceName = $scale->getEndDeviceName();
        
        // The end_device_name is the uuid_client, so we use the preloaded clients
        if ($endDeviceName && isset($clients[$endDeviceName])) {
            $clientName = $clients[$endDeviceName]->getName();
        }

        return [
            'end_device_id' => $scale->getEndDeviceId(),
            'end_device_name' => $endDeviceName,
            'client_name' => $clientName,
        ];
    }
}
