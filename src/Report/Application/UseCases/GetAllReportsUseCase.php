<?php

namespace App\Report\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Product;
use App\Entity\Client\Report;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Report\Application\DTO\GetAllReportsRequest;
use App\Report\Application\DTO\GetAllReportsResponse;
use App\Report\Application\InputPorts\GetAllReportsUseCaseInterface;
use App\Report\Infrastructure\OutputAdapters\Repositories\ReportRepository;
use Psr\Log\LoggerInterface;

class GetAllReportsUseCase implements GetAllReportsUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(GetAllReportsRequest $request): GetAllReportsResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $entityManager = $this->connectionManager->getEntityManager($client->getUuidClient());
        $reportRepository = new ReportRepository($entityManager);

        $reports = $reportRepository->findAll();

        $reportsData = array_map(
            fn (Report $report): array => $this->mapReportToArray($report),
            $reports
        );

        $this->logger->info('Reports retrieved for client', [
            'uuid_client' => $client->getUuidClient(),
            'count' => count($reportsData),
        ]);

        return new GetAllReportsResponse($reportsData);
    }

    /**
     * NUEVO: Mapea un report a array incluyendo productos específicos si existen
     */
    private function mapReportToArray(Report $report): array
    {
        $data = [
            'id' => $report->getId(),
            'name' => $report->getName(),
            'period' => $report->getPeriod(),
            'send_time' => $report->getSendTime()->format('H:i:s'),
            'report_type' => $report->getReportType(),
            'product_filter' => $report->getProductFilter(),
            'email' => $report->getEmail(),
        ];

        // NUEVO: Si tiene productos específicos, incluirlos
        if ($report->hasSpecificProducts()) {
            $products = $report->getProducts();
            $data['product_ids'] = $report->getProductIds();
            $data['products'] = array_map(function (Product $product) {
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'ean' => $product->getEan(),
                ];
            }, $products);
            $data['product_count'] = count($products);
        } else {
            $data['product_ids'] = [];
            $data['products'] = [];
            $data['product_count'] = 0;
        }

        return $data;
    }
}