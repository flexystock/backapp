<?php

namespace App\Report\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Report\Application\DTO\UpdateReportRequest;
use App\Report\Application\DTO\UpdateReportResponse;
use App\Report\Application\InputPorts\UpdateReportUseCaseInterface;
use App\Report\Infrastructure\OutputAdapters\Repositories\ReportRepository;
use Psr\Log\LoggerInterface;

class UpdateReportUseCase implements UpdateReportUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(UpdateReportRequest $request): UpdateReportResponse
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

        $timestamp = $request->getTimestamp() ?? new \DateTimeImmutable();
        $uuidUser = $request->getUuidUser() ?? 'system';

        $sendTime = $this->parseSendTime($request->getSendTime());

        $report->setName($request->getName());
        $report->setPeriod($request->getPeriod());
        $report->setSendTime($sendTime);
        $report->setReportType($request->getReportType());
        $report->setProductFilter($request->getProductFilter());
        $report->setEmail($request->getEmail());
        $report->setUuidUserModification($uuidUser);
        $report->setDatehourModification($timestamp);

        $reportRepository->save($report);
        $reportRepository->flush();

        $reportData = [
            'id' => $report->getId(),
            'name' => $report->getName(),
            'period' => $report->getPeriod(),
            'send_time' => $report->getSendTime()->format('H:i:s'),
            'report_type' => $report->getReportType(),
            'product_filter' => $report->getProductFilter(),
            'email' => $report->getEmail(),
        ];

        $this->logger->info('Report updated', [
            'uuid_client' => $uuidClient,
            'report_id' => $report->getId(),
        ]);

        return new UpdateReportResponse($reportData);
    }

    private function parseSendTime(string $sendTime): \DateTimeInterface
    {
        $parsedTime = \DateTimeImmutable::createFromFormat('H:i:s', $sendTime);
        if (false !== $parsedTime) {
            return $parsedTime;
        }

        $parsedTime = \DateTimeImmutable::createFromFormat('H:i', $sendTime);
        if (false !== $parsedTime) {
            return $parsedTime;
        }

        try {
            return new \DateTimeImmutable($sendTime);
        } catch (\Exception $exception) {
            $this->logger->error('Invalid send time received', [
                'sendTime' => $sendTime,
                'exception' => $exception->getMessage(),
            ]);

            throw new \RuntimeException('INVALID_SEND_TIME');
        }
    }
}
