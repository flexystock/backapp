<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\UseCases;

use App\ControlPanel\Scale\Application\DTO\GetScaleInfoRequest;
use App\ControlPanel\Scale\Application\DTO\GetScaleInfoResponse;
use App\ControlPanel\Scale\Application\InputPorts\GetScaleInfoUseCaseInterface;
use App\ControlPanel\Scale\Application\OutputPorts\ClientRepositoryInterface;
use App\ControlPanel\Scale\Application\OutputPorts\ScaleRepositoryInterface;
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

            $scaleInfo = $this->mapScaleToArray($scale);

            return new GetScaleInfoResponse([$scaleInfo], null, 200);
        } else {
            // Get all scales
            $this->logger->info('Executing ControlPanel GetScaleInfoUseCase for all scales');
            $scales = $this->scaleRepository->findAll();

            $scalesInfo = array_map(function ($scale) {
                return $this->mapScaleToArray($scale);
            }, $scales);

            return new GetScaleInfoResponse($scalesInfo, null, 200);
        }
    }

    private function mapScaleToArray($scale): array
    {
        $clientName = null;
        $endDeviceName = $scale->getEndDeviceName();
        
        // The end_device_name is the uuid_client, so we fetch the client name
        if ($endDeviceName) {
            $client = $this->clientRepository->findOneByUuid($endDeviceName);
            if ($client) {
                $clientName = $client->getName();
            }
        }

        return [
            'end_device_id' => $scale->getEndDeviceId(),
            'end_device_name' => $endDeviceName,
            'client_name' => $clientName,
        ];
    }
}
