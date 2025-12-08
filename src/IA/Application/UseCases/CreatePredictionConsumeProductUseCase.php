<?php

namespace App\IA\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\IA\Application\DTO\CreatePredictionConsumeProductRequest;
use App\IA\Application\DTO\CreatePredictionConsumeProductResponse;
use App\IA\Application\InputPorts\CreatePredictionConsumeProductUseCaseInterface;
use App\IA\Application\Services\PredictionService;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository;
use App\WeightAnalytics\Infrastructure\OutputAdapters\Repositories\WeightsLogRepository;
use Psr\Log\LoggerInterface;

class CreatePredictionConsumeProductUseCase implements CreatePredictionConsumeProductUseCaseInterface
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
            $prediction = $this->predictionService->calculatePrediction($weightsLog, $product);

            return new CreatePredictionConsumeProductResponse($prediction, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('Error in CreatePredictionConsumeProductUseCase: '.$e->getMessage());

            return new CreatePredictionConsumeProductResponse(null, 'INTERNAL_SERVER_ERROR', 500);
        }
    }
}
