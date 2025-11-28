<?php

namespace App\Report\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Product;
use App\Entity\Client\WeightsLog;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Report\Application\DTO\GenerateReportNowRequest;
use App\Report\Application\DTO\GenerateReportNowResponse;
use App\Report\Application\InputPorts\GenerateReportNowUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class GenerateReportNowUseCase implements GenerateReportNowUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
        private readonly MailerInterface $mailer,
        private readonly Environment $twig,
    ) {
    }

    public function execute(GenerateReportNowRequest $request): GenerateReportNowResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $uuidClient = $client->getUuidClient();
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);

        // Get products based on filter
        $products = $this->getProducts($entityManager, $request->getProductFilter());

        if (empty($products)) {
            return new GenerateReportNowResponse(
                true,
                'NO_PRODUCTS_FOUND',
                ['products_count' => 0]
            );
        }

        // Calculate stock data with yesterday comparison
        $stockData = $this->calculateStockData($entityManager, $products);

        // Generate report content
        $reportContent = $this->generateReportContent(
            $request->getName(),
            $request->getReportType(),
            $stockData
        );

        // Send email with report
        $this->sendReportEmail(
            $request->getEmail(),
            $request->getName(),
            $request->getReportType(),
            $reportContent
        );

        $this->logger->info('Report generated and sent', [
            'uuid_client' => $uuidClient,
            'report_name' => $request->getName(),
            'report_type' => $request->getReportType(),
            'email' => $request->getEmail(),
            'products_count' => count($products),
        ]);

        return new GenerateReportNowResponse(
            true,
            'REPORT_GENERATED_AND_SENT',
            [
                'report_name' => $request->getName(),
                'report_type' => $request->getReportType(),
                'email' => $request->getEmail(),
                'products_count' => count($stockData),
            ]
        );
    }

    /**
     * @return array<Product>
     */
    private function getProducts(object $entityManager, string $productFilter): array
    {
        $productRepository = $entityManager->getRepository(Product::class);
        $allProducts = $productRepository->findAll();

        if ('all' === $productFilter) {
            return $allProducts;
        }

        // Filter products below minimum stock (using minPercentage)
        return array_filter($allProducts, function (Product $product) {
            $stock = $product->getStock();
            $minPercentage = $product->getMinPercentage();

            // If minPercentage is 0, skip the product (no minimum threshold defined)
            if (0 === $minPercentage) {
                return false;
            }

            // Include products with null stock (no data) or stock below the minimum threshold
            if (null === $stock) {
                return true;
            }

            // Product is below stock if current stock percentage is less than minPercentage
            return $stock < $minPercentage;
        });
    }

    /**
     * @param array<Product> $products
     *
     * @return array<array<string, mixed>>
     */
    private function calculateStockData(object $entityManager, array $products): array
    {
        $stockData = [];
        $yesterday = (new \DateTimeImmutable())->modify('-1 day')->setTime(23, 59, 59);

        foreach ($products as $product) {
            $currentStock = $product->getStock() ?? 0;

            // Get yesterday's stock from weights_log
            [$yesterdayStock, $hasHistoricalData] = $this->getStockAtDateTime($entityManager, $product, $yesterday);

            $stockDifference = $hasHistoricalData ? $currentStock - $yesterdayStock : null;

            $stockData[] = [
                'product_id' => $product->getId(),
                'product_uuid' => $product->getUuid(),
                'product_name' => $product->getName(),
                'ean' => $product->getEan(),
                'current_stock' => $currentStock,
                'yesterday_stock' => $yesterdayStock,
                'stock_difference' => $stockDifference,
                'has_historical_data' => $hasHistoricalData,
                'min_percentage' => $product->getMinPercentage(),
            ];
        }

        return $stockData;
    }

    /**
     * @return array{float, bool} Returns [stock value, has historical data flag]
     */
    private function getStockAtDateTime(object $entityManager, Product $product, \DateTimeInterface $dateTime): array
    {
        $qb = $entityManager->createQueryBuilder();

        $qb->select('w')
            ->from(WeightsLog::class, 'w')
            ->where('w.product = :product')
            ->andWhere('w.date <= :dateTime')
            ->setParameter('product', $product)
            ->setParameter('dateTime', $dateTime)
            ->orderBy('w.date', 'DESC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result instanceof WeightsLog) {
            return [$result->getChargePercentage(), true];
        }

        // If no historical data, return current stock with flag indicating no historical data
        return [$product->getStock() ?? 0, false];
    }

    /**
     * @param array<array<string, mixed>> $stockData
     */
    private function generateReportContent(string $reportName, string $reportType, array $stockData): string
    {
        if ('csv' === $reportType) {
            return $this->generateCsvContent($stockData);
        }

        return $this->generatePdfContent($reportName, $stockData);
    }

    /**
     * @param array<array<string, mixed>> $stockData
     */
    private function generateCsvContent(array $stockData): string
    {
        $output = fopen('php://temp', 'r+');

        // CSV Header
        fputcsv($output, [
            'ID Producto',
            'UUID Producto',
            'Nombre Producto',
            'EAN',
            'Stock Actual',
            'Stock Ayer (23:59:59)',
            'Diferencia Stock',
            'Datos Históricos',
            'Porcentaje Mínimo',
        ]);

        // CSV Data
        foreach ($stockData as $row) {
            $stockDifference = null !== $row['stock_difference']
                ? number_format($row['stock_difference'], 2)
                : 'N/A';

            fputcsv($output, [
                $row['product_id'],
                $row['product_uuid'],
                $row['product_name'],
                $row['ean'] ?? '',
                number_format($row['current_stock'], 2),
                number_format($row['yesterday_stock'], 2),
                $stockDifference,
                $row['has_historical_data'] ? 'Sí' : 'No',
                $row['min_percentage'],
            ]);
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content;
    }

    /**
     * @param array<array<string, mixed>> $stockData
     */
    private function generatePdfContent(string $reportName, array $stockData): string
    {
        return $this->twig->render('report/stock_report.html.twig', [
            'report_name' => $reportName,
            'generated_at' => new \DateTimeImmutable(),
            'stock_data' => $stockData,
        ]);
    }

    private function sendReportEmail(string $emailTo, string $reportName, string $reportType, string $content): void
    {
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($emailTo)
            ->subject('Informe de Stock: '.$reportName);

        if ('csv' === $reportType) {
            $email->text('Adjunto encontrará el informe de stock solicitado.')
                ->attach($content, $reportName.'.csv', 'text/csv');
        } else {
            $email->html($content);
        }

        $this->mailer->send($email);
    }
}
