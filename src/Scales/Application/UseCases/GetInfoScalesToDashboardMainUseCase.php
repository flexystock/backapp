<?php

namespace App\Scales\Application\UseCases;

use App\Entity\Client\Scales;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\GetInfoScalesToDashboardMainRequest;
use App\Scales\Application\DTO\GetInfoScalesToDashboardMainResponse;
use App\Scales\Application\InputPorts\GetInfoScalesToDashboardMainUseCaseInterface;
use App\Scales\Infrastructure\OutputAdapters\Repositories\ScalesRepository;
use Psr\Log\LoggerInterface;

class GetInfoScalesToDashboardMainUseCase implements GetInfoScalesToDashboardMainUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(GetInfoScalesToDashboardMainRequest $request): GetInfoScalesToDashboardMainResponse
    {
        $uuidClient = $request->getUuidClient();

        try {
            $em = $this->connectionManager->getEntityManager($uuidClient);
            $repo = new ScalesRepository($em);
            $scales = $repo->findAllByUuidClient($uuidClient);
            $data = array_map(fn(Scales $s) => $this->serializeScale($s), $scales);

            return new GetInfoScalesToDashboardMainResponse($data, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('GetInfoScalesToDashboardMainUseCase: Error', ['exception' => $e]);

            return new GetInfoScalesToDashboardMainResponse(null, 'Internal Server Error', 500);
        }
    }

    private function serializeScale(Scales $scale): array
    {
        return [
            'uuid' => $scale->getUuid(),
            'end_device_id' => $scale->getEndDeviceId(),
            'voltage_min' => $scale->getVoltageMin(),
            'voltage_percentage' => $scale->getVoltagePercentage(),
            'last_send' => $scale->getLastSend()?->format('Y-m-d H:i:s'),
            'product_asociate' => $scale->getProduct()?->getName(),
        ];
    }
}
