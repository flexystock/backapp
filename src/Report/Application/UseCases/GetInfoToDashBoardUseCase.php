<?php

namespace App\Report\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Report\Application\DTO\GetInfoToDashBoardRequest;
use App\Report\Application\DTO\GetInfoToDashBoardResponse;
use App\Report\Application\InputPorts\GetInfoToDashBoardUseCaseInterface;
use App\Report\Infrastructure\OutputAdapters\Repositories\ReportRepository;
use Psr\Log\LoggerInterface;

class GetInfoToDashBoardUseCase implements GetInfoToDashBoardUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(GetInfoToDashBoardRequest $request): GetInfoToDashBoardResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $entityManager = $this->connectionManager->getEntityManager($client->getUuidClient());
        $reportRepository = new ReportRepository($entityManager);

        $totalReports = $reportRepository->count();
        $reports = $reportRepository->findAll();

        $reportsByType = [];
        $reportsByPeriod = [];

        foreach ($reports as $report) {
            $type = $report->getReportType();
            $period = $report->getPeriod();

            if (!isset($reportsByType[$type])) {
                $reportsByType[$type] = 0;
            }
            ++$reportsByType[$type];

            if (!isset($reportsByPeriod[$period])) {
                $reportsByPeriod[$period] = 0;
            }
            ++$reportsByPeriod[$period];
        }

        $dashboardInfo = [
            'total_reports' => $totalReports,
            'reports_by_type' => $reportsByType,
            'reports_by_period' => $reportsByPeriod,
        ];

        $this->logger->info('Dashboard info retrieved for client', [
            'uuid_client' => $client->getUuidClient(),
            'total_reports' => $totalReports,
        ]);

        return new GetInfoToDashBoardResponse($dashboardInfo);
    }
}
