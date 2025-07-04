<?php

namespace App\WeightAnalytics\Infrastructure\InputAdapters;
use App\WeightAnalytics\Application\DTO\GetProductWeightSummaryRequest;
use App\WeightAnalytics\Application\DTO\GetProductWeightSummaryResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\WeightAnalytics\Application\InputPorts\GetProductWeightSummaryUseCaseInterface;

class GetProductWeightSummaryController extends AbstractController
{
    private LoggerInterface $logger;
    private GetProductWeightSummaryUseCaseInterface $getProductWeightSummaryUseCase;

    public function __construct(
        LoggerInterface $logger,
        GetProductWeightSummaryUseCaseInterface $getProductWeightSummaryUseCase
    ) {
        $this->logger = $logger;
        $this->getProductWeightSummaryUseCase = $getProductWeightSummaryUseCase;
    }

    #[Route('/api/weight_analytics/product_weight_summary', name: 'api_product_weight_summary', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $productId = $data['productId'] ?? null;
        $from = $data['from'] ?? null;
        $to = $data['to'] ?? null;

        if (!$uuidClient || !$productId) {
            return new JsonResponse(['error' => 'uuidClient and productId are required'], 400);
        }

        $dto = new GetProductWeightSummaryRequest($uuidClient, $productId, $from, $to);

        $response = $this->getProductWeightSummaryUseCase->execute($dto);

        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['summary' => $response->getSummary()], 200);
    }


}