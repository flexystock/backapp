<?php

namespace App\IA\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\IA\Application\DTO\CreatePredictionConsumeProductRequest;
use App\IA\Application\DTO\CreatePredictionConsumeProductResponse;
use App\IA\Application\InputPorts\CreatePredictionConsumeProductUseCaseInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository;
use App\WeightAnalytics\Infrastructure\OutputAdapters\Repositories\WeightsLogRepository;
use Psr\Log\LoggerInterface;

class CreatePredictionConsumeProductUseCase implements CreatePredictionConsumeProductUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        ClientConnectionManager $connectionManager,
        LoggerInterface $logger,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
        $this->clientRepository = $clientRepository;
    }

    public function execute(CreatePredictionConsumeProductRequest $request): CreatePredictionConsumeProductResponse
    {
        try {
            $uuidClient = $request->getUuidClient();
            $productId = $request->getProductId();

            // Verify client exists
            $client = $this->clientRepository->findByUuid($uuidClient);
            if (!$client) {
                return new CreatePredictionConsumeProductResponse(null, 'CLIENT_NOT_FOUND', 404);
            }

            // Get client database entity manager
            $em = $this->connectionManager->getEntityManager($uuidClient);
            
            // Get repositories
            $productRepository = new ProductRepository($em);
            $weightsLogRepository = new WeightsLogRepository($em);

            // Get product
            $product = $productRepository->findById($productId);
            if (!$product) {
                return new CreatePredictionConsumeProductResponse(null, 'PRODUCT_NOT_FOUND', 404);
            }

            // Get weight history for the product (last 30 days)
            $from = new \DateTime('-30 days');
            $weightsLog = $weightsLogRepository->getProductWeightSummary($productId, $from, null);

            if (empty($weightsLog)) {
                return new CreatePredictionConsumeProductResponse(null, 'INSUFFICIENT_DATA', 400);
            }

            // Calculate consumption prediction
            $prediction = $this->calculatePrediction($weightsLog, $product);

            return new CreatePredictionConsumeProductResponse($prediction, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('Error in CreatePredictionConsumeProductUseCase: '.$e->getMessage());

            return new CreatePredictionConsumeProductResponse(null, 'INTERNAL_SERVER_ERROR', 500);
        }
    }

    private function calculatePrediction(array $weightsLog, $product): array
    {
        // Prepare data for linear regression
        $dataPoints = [];
        $firstTimestamp = null;

        foreach ($weightsLog as $log) {
            $timestamp = $log->getDate()->getTimestamp();
            if ($firstTimestamp === null) {
                $firstTimestamp = $timestamp;
            }
            // Convert timestamp to days from start
            $daysSinceStart = ($timestamp - $firstTimestamp) / 86400;
            $dataPoints[] = [
                'x' => $daysSinceStart,
                'y' => (float) $log->getAdjustWeight(),
            ];
        }

        if (count($dataPoints) < 2) {
            throw new \RuntimeException('INSUFFICIENT_DATA');
        }

        // Calculate linear regression (y = mx + b)
        $regression = $this->linearRegression($dataPoints);
        $slope = $regression['slope'];
        $intercept = $regression['intercept'];

        // Get current stock and minimum stock
        $currentWeight = end($dataPoints)['y'];
        $minStock = $product->getStock() ?? 0;

        // Calculate days until stock depletes to minimum
        $daysUntilMinStock = null;
        $stockDepletionDate = null;
        $recommendedRestockDate = null;

        if ($slope < 0) {
            // Product is being consumed (negative slope)
            $daysUntilMinStock = ($minStock - $currentWeight) / $slope;
            
            if ($daysUntilMinStock > 0) {
                $stockDepletionDate = new \DateTime();
                $stockDepletionDate->modify('+'.round($daysUntilMinStock).' days');

                // Recommend restocking based on days_serve_order
                $daysServeOrder = $product->getDaysServeOrder();
                $recommendedRestockDate = clone $stockDepletionDate;
                $recommendedRestockDate->modify('-'.$daysServeOrder.' days');
            }
        }

        return [
            'product_id' => $product->getId(),
            'product_name' => $product->getName(),
            'current_weight' => $currentWeight,
            'min_stock' => $minStock,
            'consumption_rate' => abs($slope), // kg/day
            'days_until_min_stock' => $daysUntilMinStock ? round($daysUntilMinStock, 2) : null,
            'stock_depletion_date' => $stockDepletionDate ? $stockDepletionDate->format('Y-m-d H:i:s') : null,
            'recommended_restock_date' => $recommendedRestockDate ? $recommendedRestockDate->format('Y-m-d H:i:s') : null,
            'days_serve_order' => $product->getDaysServeOrder(),
        ];
    }

    private function linearRegression(array $dataPoints): array
    {
        $n = count($dataPoints);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($dataPoints as $point) {
            $sumX += $point['x'];
            $sumY += $point['y'];
            $sumXY += $point['x'] * $point['y'];
            $sumX2 += $point['x'] * $point['x'];
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        return [
            'slope' => $slope,
            'intercept' => $intercept,
        ];
    }
}
