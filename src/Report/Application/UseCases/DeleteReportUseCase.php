<?php

namespace App\Report\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Report\Application\DTO\DeleteReportRequest;
use App\Report\Application\DTO\DeleteReportResponse;
use App\Report\Application\InputPorts\DeleteReportUseCaseInterface;
use App\Report\Infrastructure\OutputAdapters\Repositories\ReportRepository;
use Psr\Log\LoggerInterface;

class DeleteReportUseCase implements DeleteReportUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(DeleteReportRequest $request): DeleteReportResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $uuidClient = $client->getUuidClient();
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);
        $reportRepository = new ReportRepository($entityManager);

        $report = $reportRepository->findById($request->getReportId());
        if (!$report) {
            throw new \RuntimeException('REPORT_NOT_FOUND');
        }

        $reportId = $report->getId();

        $reportRepository->remove($report);
        $reportRepository->flush();

        $this->logger->info('Report deleted', [
            'uuid_client' => $uuidClient,
            'report_id' => $reportId,
        ]);

        return new DeleteReportResponse($reportId);
    }
}
