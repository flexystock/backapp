<?php

namespace App\Report\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Report\Application\DTO\GetReportRequest;
use App\Report\Application\DTO\GetReportResponse;
use App\Report\Application\InputPorts\GetReportUseCaseInterface;
use App\Report\Infrastructure\OutputAdapters\Repositories\ReportRepository;
use Psr\Log\LoggerInterface;

class GetReportUseCase implements GetReportUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(GetReportRequest $request): GetReportResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $entityManager = $this->connectionManager->getEntityManager($client->getUuidClient());
        $reportRepository = new ReportRepository($entityManager);

        $report = $reportRepository->findById($request->getReportId());
        if (!$report) {
            throw new \RuntimeException('REPORT_NOT_FOUND');
        }

        $reportData = [
            'id' => $report->getId(),
            'name' => $report->getName(),
            'period' => $report->getPeriod(),
            'send_time' => $report->getSendTime()->format('H:i:s'),
            'report_type' => $report->getReportType(),
            'product_filter' => $report->getProductFilter(),
            'email' => $report->getEmail(),
        ];

        $this->logger->info('Report retrieved', [
            'uuid_client' => $client->getUuidClient(),
            'report_id' => $report->getId(),
        ]);

        return new GetReportResponse($reportData);
    }
}
