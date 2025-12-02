<?php

namespace App\Report\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Product;
use App\Entity\Client\WeightsLog;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Product\Infrastructure\OutputAdapters\Repositories\WeightsLogRepository;
use App\Report\Application\DTO\GenerateReportNowRequest;
use App\Report\Application\DTO\GenerateReportNowResponse;
use App\Report\Application\InputPorts\GenerateReportNowUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Dompdf\Dompdf;
use Dompdf\Options;

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
        $weightsLogRepository = new WeightsLogRepository($entityManager);

        // Get products based on filter
        $products = $this->getProducts($entityManager, $request->getProductFilter(), $weightsLogRepository);

        if (empty($products)) {
            return new GenerateReportNowResponse(
                true,
                'NO_PRODUCTS_FOUND',
                ['products_count' => 0]
            );
        }

        // Calculate stock data based on period
        $stockData = $this->calculateStockDataByPeriod(
            $entityManager,
            $products,
            $weightsLogRepository,
            $request->getPeriod()
        );

        // Generate report content
        $reportContent = $this->generateReportContent(
            $request->getName(),
            $request->getReportType(),
            $request->getPeriod(),
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
            'period' => $request->getPeriod(),
            'email' => $request->getEmail(),
            'products_count' => count($products),
        ]);

        return new GenerateReportNowResponse(
            true,
            'REPORT_GENERATED_AND_SENT',
            [
                'report_name' => $request->getName(),
                'report_type' => $request->getReportType(),
                'period' => $request->getPeriod(),
                'email' => $request->getEmail(),
                'products_count' => count($stockData),
            ]
        );
    }

    /**
     * @return array<Product>
     */
    private function getProducts(object $entityManager, string $productFilter, WeightsLogRepository $weightsLogRepository): array
    {
        $productRepository = $entityManager->getRepository(Product::class);
        $allProducts = $productRepository->findAll();

        if ('all' === $productFilter) {
            return $allProducts;
        }

        // Filter products below minimum stock
        return array_filter($allProducts, function (Product $product) use ($weightsLogRepository) {
            $minStock = $product->getStock();

            if (0 === $minStock || null === $minStock) {
                return false;
            }

            $conversionInfo = $this->getConversionInfo($product);
            $realWeightSum = $weightsLogRepository->getLatestTotalRealWeightByProduct($product->getId()) ?? 0;
            $conversionFactor = $conversionInfo['conversion_factor'];
            $currentStock = $conversionFactor > 0 ? round($realWeightSum / $conversionFactor, 1) : 0;

            return $currentStock < $minStock;
        });
    }

    /**
     * @param array<Product> $products
     * @return array<array<string, mixed>>
     */
    private function calculateStockDataByPeriod(
        object $entityManager,
        array $products,
        WeightsLogRepository $weightsLogRepository,
        string $period
    ): array {
        return match ($period) {
            'daily' => $this->calculateDailyStockData($entityManager, $products, $weightsLogRepository),
            'weekly' => $this->calculateWeeklyStockData($entityManager, $products, $weightsLogRepository),
            'monthly' => $this->calculateMonthlyStockData($entityManager, $products, $weightsLogRepository),
            default => $this->calculateDailyStockData($entityManager, $products, $weightsLogRepository),
        };
    }

    /**
     * @param array<Product> $products
     * @return array<array<string, mixed>>
     */
    private function calculateDailyStockData(object $entityManager, array $products, WeightsLogRepository $weightsLogRepository): array
    {
        $stockData = [];
        $yesterday = (new \DateTimeImmutable())->modify('-1 day')->setTime(23, 59, 59);

        foreach ($products as $product) {
            $conversionInfo = $this->getConversionInfo($product);
            $realWeightSum = $weightsLogRepository->getLatestTotalRealWeightByProduct($product->getId()) ?? 0;
            $conversionFactor = $conversionInfo['conversion_factor'];
            [$yesterdayRealWeigh, $hasHistoricalData] = $this->getStockAtDateTime($entityManager, $product, $yesterday);
            $yesterdayStock = $conversionFactor > 0 ? round($yesterdayRealWeigh / $conversionFactor, 1) : 0;
            $currentStock = $conversionFactor > 0 ? round($realWeightSum / $conversionFactor, 1) : 0;
            $stockDifference = $hasHistoricalData ? $currentStock - $yesterdayStock : null;

            $stockData[] = [
                'product_name' => $product->getName(),
                'ean' => $product->getEan(),
                'current_stock' => $currentStock,
                'yesterday_stock' => $yesterdayStock,
                'stock_difference' => $stockDifference,
                'has_historical_data' => $hasHistoricalData,
                'min_stock' => $product->getStock(),
            ];
        }
        return $stockData;
    }

    /**
     * @param array<Product> $products
     * @return array<array<string, mixed>>
     */
    private function calculateWeeklyStockData(object $entityManager, array $products, WeightsLogRepository $weightsLogRepository): array
    {
        $stockData = [];
        $today = new \DateTimeImmutable();

        // Generar fechas de los últimos 7 días
        $dates = [];
        $dateLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->modify("-{$i} days")->setTime(23, 59, 59);
            $dates[] = $date;
            $dateLabels[] = $date->format('D d/m'); // Ej: "Lun 25/11"
        }

        foreach ($products as $product) {
            $conversionInfo = $this->getConversionInfo($product);
            $conversionFactor = $conversionInfo['conversion_factor'];

            $productData = [
                'product_name' => $product->getName(),
                'ean' => $product->getEan(),
                'min_stock' => $product->getStock(),
                'daily_stocks' => [],
            ];

            // Obtener stock de cada día
            foreach ($dates as $index => $date) {
                [$realWeight, $hasData] = $this->getStockAtDateTime($entityManager, $product, $date);
                $stock = $conversionFactor > 0 ? round($realWeight / $conversionFactor, 1) : 0;

                $productData['daily_stocks'][$dateLabels[$index]] = [
                    'stock' => $stock,
                    'has_data' => $hasData,
                ];
            }

            $stockData[] = $productData;
        }

        return $stockData;
    }

    /**
     * @param array<Product> $products
     * @return array<array<string, mixed>>
     */
    private function calculateMonthlyStockData(object $entityManager, array $products, WeightsLogRepository $weightsLogRepository): array
    {
        $stockData = [];
        $today = new \DateTimeImmutable();

        // Generar fechas de los últimos 30 días
        $dates = [];
        $dateLabels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->modify("-{$i} days")->setTime(23, 59, 59);
            $dates[] = $date;
            $dateLabels[] = $date->format('d/m'); // Ej: "01/11"
        }

        foreach ($products as $product) {
            $conversionInfo = $this->getConversionInfo($product);
            $conversionFactor = $conversionInfo['conversion_factor'];

            $productData = [
                'product_name' => $product->getName(),
                'ean' => $product->getEan(),
                'min_stock' => $product->getStock(),
                'daily_stocks' => [],
            ];

            // Obtener stock de cada día
            foreach ($dates as $index => $date) {
                [$realWeight, $hasData] = $this->getStockAtDateTime($entityManager, $product, $date);
                $stock = $conversionFactor > 0 ? round($realWeight / $conversionFactor, 1) : 0;

                $productData['daily_stocks'][$dateLabels[$index]] = [
                    'stock' => $stock,
                    'has_data' => $hasData,
                ];
            }

            $stockData[] = $productData;
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
            return [$result->getRealWeight(), true];
        }

        return [0, false];
    }

    /**
     * @param array<array<string, mixed>> $stockData
     */
    private function generateReportContent(string $reportName, string $reportType, string $period, array $stockData): string
    {
        if ('csv' === $reportType) {
            return $this->generateCsvContent($period, $stockData);
        }

        return $this->generatePdfContent($reportName, $period, $stockData);
    }

    /**
     * @param array<array<string, mixed>> $stockData
     */
    private function generateCsvContent(string $period, array $stockData): string
    {
        $output = fopen('php://temp', 'r+');

        if ('daily' === $period) {
            // CSV Header paraDaily
            fputcsv($output, [
                'Nombre Producto',
                'EAN',
                'Stock Actual',
                'Stock Ayer (23:59:59)',
                'Diferencia Stock',
                'Datos Históricos',
                'Stock Mínimo',
            ]);

            // CSV Data para Daily
            foreach ($stockData as $row) {
                $stockDifference = null !== $row['stock_difference']
                    ? number_format($row['stock_difference'], 2)
                    : 'N/A';

                fputcsv($output, [
                    $row['product_name'],
                    $row['ean'] ?? '',
                    number_format($row['current_stock'], 2),
                    number_format($row['yesterday_stock'], 2),
                    $stockDifference,
                    $row['has_historical_data'] ? 'Sí' : 'No',
                    $row['min_stock'],
                ]);
            }
        } else {
            // CSV Header para Weekly/Monthly (dinámico)
            $firstProduct = $stockData[0] ?? null;
            if (!$firstProduct) {
                fputcsv($output, ['No hay datos disponibles']);
            } else {
                $headers = ['Nombre Producto', 'EAN'];
                foreach (array_keys($firstProduct['daily_stocks']) as $dateLabel) {
                    $headers[] = $dateLabel;
                }
                $headers[] = 'Stock Mínimo';
                fputcsv($output, $headers);

                // CSV Data para Weekly/Monthly
                foreach ($stockData as $row) {
                    $csvRow = [
                        $row['product_name'],
                        $row['ean'] ?? '',
                    ];

                    foreach ($row['daily_stocks'] as $dayData) {
                        $csvRow[] = number_format($dayData['stock'], 2);
                    }

                    $csvRow[] = $row['min_stock'];
                    fputcsv($output, $csvRow);
                }
            }
        }

        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $content;
    }

    /**
     * @param array<array<string, mixed>> $stockData
     */
    private function generatePdfContent(string $reportName, string $period, array $stockData): string
    {
        $templateName = match ($period) {
            'daily' => 'report/stock_report_daily.html.twig',
            'weekly' => 'report/stock_report_weekly.html.twig',
            'monthly' => 'report/stock_report_monthly.html.twig',
            default => 'report/stock_report_daily.html.twig',
        };

        // Preparar etiquetas de fechas para la plantilla
        $dateLabels = [];
        if (!empty($stockData) && isset($stockData[0]['daily_stocks'])) {
            $dateLabels = array_keys($stockData[0]['daily_stocks']);
        }

        $html = $this->twig->render($templateName, [
            'report_name' => $reportName,
            'generated_at' => new \DateTimeImmutable(),
            'stock_data' => $stockData,
            'date_labels' => $dateLabels,
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // Usar landscape para weekly/monthly si hay muchas columnas
        $orientation = ('daily' === $period) ? 'portrait' : 'landscape';
        $dompdf->setPaper('A4', $orientation);

        $dompdf->render();

        return $dompdf->output();
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
            $email->text('Adjunto encontrará el informe de stock en formato PDF.')
                ->attach($content, $reportName.'.pdf', 'application/pdf');
        }

        $this->mailer->send($email);
    }

    /**
     * Obtiene la información de conversión para un producto.
     */
    private function getConversionInfo(Product $product): array
    {
        $mainUnit = (int) $product->getMainUnit();

        switch ($mainUnit) {
            case 1:
                return [
                    'main_unit' => $mainUnit,
                    'unit_name' => $product->getNameUnit1(),
                    'conversion_factor' => $product->getWeightUnit1(),
                ];
            case 2:
                return [
                    'main_unit' => $mainUnit,
                    'unit_name' => $product->getNameUnit2(),
                    'conversion_factor' => $product->getWeightUnit2(),
                ];
            case 0:
            default:
                return [
                    'main_unit' => 0,
                    'unit_name' => 'Kg',
                    'conversion_factor' => 1,
                ];
        }
    }
}