<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\UseCases;

use App\ControlPanel\Scale\Application\DTO\GetScaleInfoRequest;
use App\ControlPanel\Scale\Application\DTO\GetScaleInfoResponse;
use App\ControlPanel\Scale\Application\InputPorts\GetScaleInfoUseCaseInterface;
use App\ControlPanel\Scale\Application\OutputPorts\ClientRepositoryInterface;
use App\ControlPanel\Scale\Application\OutputPorts\ClientScalesRepositoryInterface;
use App\ControlPanel\Scale\Application\OutputPorts\ScaleRepositoryInterface;
use App\Entity\Main\PoolTtnDevice;
use Psr\Log\LoggerInterface;

class GetScaleInfoUseCase implements GetScaleInfoUseCaseInterface
{
    private LoggerInterface $logger;
    private ScaleRepositoryInterface $scaleRepository;
    private ClientRepositoryInterface $clientRepository;
    private ClientScalesRepositoryInterface $clientScalesRepository;

    public function __construct(
        LoggerInterface $logger,
        ScaleRepositoryInterface $scaleRepository,
        ClientRepositoryInterface $clientRepository,
        ClientScalesRepositoryInterface $clientScalesRepository
    ) {
        $this->logger = $logger;
        $this->scaleRepository = $scaleRepository;
        $this->clientRepository = $clientRepository;
        $this->clientScalesRepository = $clientScalesRepository;
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

            // Load client and voltage percentage for single scale
            $endDeviceName = $scale->getEndDeviceName();
            $clients = [];
            $voltagePercentages = [];
            
            if ($endDeviceName) {
                $clients = $this->clientRepository->findByUuids([$endDeviceName]);
                $scalesByClient = [$endDeviceName => [$endDeviceId]];
                $voltagePercentages = $this->clientScalesRepository->getVoltagePercentagesByClient($scalesByClient);
            }

            $scaleInfo = $this->mapScaleToArray($scale, $clients, $voltagePercentages);

            return new GetScaleInfoResponse([$scaleInfo], null, 200);
        } else {
            // Get all scales
            $this->logger->info('Executing ControlPanel GetScaleInfoUseCase for all scales');
            $scales = $this->scaleRepository->findAll();

            // Batch load clients to avoid N+1 query problem
            $clientUuids = [];
            $scalesByClient = [];
            
            foreach ($scales as $scale) {
                $endDeviceName = $scale->getEndDeviceName();
                if ($endDeviceName) {
                    $clientUuids[] = $endDeviceName;
                    
                    if (!isset($scalesByClient[$endDeviceName])) {
                        $scalesByClient[$endDeviceName] = [];
                    }
                    $scalesByClient[$endDeviceName][] = $scale->getEndDeviceId();
                }
            }
            
            $clients = $this->clientRepository->findByUuids(array_unique($clientUuids));
            
            // Batch load voltage percentages from client databases
            $voltagePercentages = $this->clientScalesRepository->getVoltagePercentagesByClient($scalesByClient);

            // Map scales to array
            $scalesInfo = [];
            foreach ($scales as $scale) {
                $scalesInfo[] = $this->mapScaleToArray($scale, $clients, $voltagePercentages);
            }

            return new GetScaleInfoResponse($scalesInfo, null, 200);
        }
    }

    private function mapScaleToArray(PoolTtnDevice $scale, array $clients = [], array $voltagePercentages = []): array
    {
        $clientName = null;
        $endDeviceName = $scale->getEndDeviceName();
        $endDeviceId = $scale->getEndDeviceId();
        
        // The end_device_name is the uuid_client, so we use the preloaded clients
        if ($endDeviceName && isset($clients[$endDeviceName])) {
            $clientName = $clients[$endDeviceName]->getName();
        }

        return [
            'end_device_id' => $endDeviceId,
            'end_device_name' => $endDeviceName,
            'client_name' => $clientName,
            'voltage_percentage' => $voltagePercentages[$endDeviceId] ?? null,
        ];
    }
}
