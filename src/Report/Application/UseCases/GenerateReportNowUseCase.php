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

        // Calculate stock data with yesterday comparison
        $stockData = $this->calculateStockData($entityManager, $products, $weightsLogRepository);

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
    private function getProducts(object $entityManager, string $productFilter, WeightsLogRepository $weightsLogRepository): array
    {
        $productRepository = $entityManager->getRepository(Product::class);
        $allProducts = $productRepository->findAll();
        //var_dump($allProducts);

        if ('all' === $productFilter) {
            return $allProducts;
        }

        // Filter products below minimum stock
        // El stock mínimo está en $product->getMinPercentage()
        // El stock actual (real) viene de weights_log
        return array_filter($allProducts, function (Product $product) use ($weightsLogRepository) {
            $minStock = $product->getStock();

            // Si no hay mínimo definido, no incluir el producto
            if (0 === $minStock || null === $minStock) {
                return false;
            }

            // Calcular el stock actual real desde weights_log (igual que en calculateStockData)
            $conversionInfo = $this->getConversionInfo($product);
            $realWeightSum = $weightsLogRepository->getLatestTotalRealWeightByProduct($product->getId()) ?? 0;
            $conversionFactor = $conversionInfo['conversion_factor'];
            $currentStock = $conversionFactor > 0 ? round($realWeightSum / $conversionFactor, 1) : 0;

            // Incluir si el stock actual es menor que el mínimo
            return $currentStock < $minStock;
        });
    }

    /**
     * @param array<Product> $products
     *
     * @return array<array<string, mixed>>
     */
    private function calculateStockData(object $entityManager, array $products, WeightsLogRepository $weightsLogRepository): array
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

            // Get yesterday's stock from weights_log

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
            'Nombre Producto',
            'EAN',
            'Stock Actual',
            'Stock Ayer (23:59:59)',
            'Diferencia Stock',
            'Datos Históricos',
            'Stock Mínimo',
        ]);

        // CSV Data
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
        $html = $this->twig->render('report/stock_report.html.twig', [
            'report_name' => $reportName,
            'generated_at' => new \DateTimeImmutable(),
            'stock_data' => $stockData,
        ]);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
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
     *
     * Se utiliza el campo main_unit para determinar la unidad:
     * - 0: Se consideran Kg (por defecto).
     * - 1: Se utiliza name_unit1 y weight_unit1.
     * - 2: Se utiliza name_unit2 y weight_unit2.
     */
    private function getConversionInfo(Product $product): array
    {
        // Convertir a entero para evitar confusiones (en caso de que se guarde como string)
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
