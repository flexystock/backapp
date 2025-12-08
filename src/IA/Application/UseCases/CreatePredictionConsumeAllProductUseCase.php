<?php

namespace App\IA\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\IA\Application\DTO\CreatePredictionConsumeAllProductRequest;
use App\IA\Application\DTO\CreatePredictionConsumeAllProductResponse;
use App\IA\Application\InputPorts\CreatePredictionConsumeAllProductUseCaseInterface;
use App\IA\Application\Services\PredictionService;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository;
use App\WeightAnalytics\Infrastructure\OutputAdapters\Repositories\WeightsLogRepository;
use Psr\Log\LoggerInterface;

class CreatePredictionConsumeAllProductUseCase implements CreatePredictionConsumeAllProductUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private ClientRepositoryInterface $clientRepository;
    private PredictionService $predictionService;

    public function __construct(
        ClientConnectionManager $connectionManager,
        LoggerInterface $logger,
        ClientRepositoryInterface $clientRepository,
        PredictionService $predictionService
    ) {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
        $this->clientRepository = $clientRepository;
        $this->predictionService = $predictionService;
    }

    public function execute(CreatePredictionConsumeAllProductRequest $request): CreatePredictionConsumeAllProductResponse
    {
        try {
            $uuidClient = $request->getUuidClient();

            // Verify client exists
            $client = $this->clientRepository->findByUuid($uuidClient);
            if (!$client) {
                return new CreatePredictionConsumeAllProductResponse(null, 'CLIENT_NOT_FOUND', 404);
            }

            // Get client database entity manager
            $em = $this->connectionManager->getEntityManager($uuidClient);
            
            // Get repositories
            $productRepository = new ProductRepository($em);
            $weightsLogRepository = new WeightsLogRepository($em);

            // Get all products for the client
            $products = $productRepository->findAll();

            if (empty($products)) {
                return new CreatePredictionConsumeAllProductResponse([], null, 200);
            }

            $predictions = [];
            $from = new \DateTime('-30 days');

            foreach ($products as $product) {
                try {
                    // Get weight history for the product
                    $weightsLog = $weightsLogRepository->getProductWeightSummary($product->getId(), $from, null);

                    if (empty($weightsLog) || count($weightsLog) < 2) {
                        // Skip products with insufficient data
                        continue;
                    }

                    // Calculate consumption prediction
                    $prediction = $this->predictionService->calculatePrediction($weightsLog, $product);
                    $predictions[] = $prediction;
                } catch (\Exception $e) {
                    // Log error but continue with other products
                    $this->logger->warning('Error calculating prediction for product '.$product->getId().': '.$e->getMessage());
                }
            }

            return new CreatePredictionConsumeAllProductResponse($predictions, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('Error in CreatePredictionConsumeAllProductUseCase: '.$e->getMessage());

            return new CreatePredictionConsumeAllProductResponse(null, 'INTERNAL_SERVER_ERROR', 500);
        }
    }
}
