<?php

namespace App\WeightAnalytics\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\ClientAccessControlTrait;
use App\Security\RequiresPermission;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\WeightAnalytics\Application\DTO\GetProductWeightSummaryRequest;
use App\WeightAnalytics\Application\InputPorts\GetProductWeightSummaryUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetProductWeightSummaryController extends AbstractController
{
    use PermissionControllerTrait;
    use ClientAccessControlTrait;

    private LoggerInterface $logger;
    private GetProductWeightSummaryUseCaseInterface $getProductWeightSummaryUseCase;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        LoggerInterface $logger,
        GetProductWeightSummaryUseCaseInterface $getProductWeightSummaryUseCase,
        PermissionService $permissionService,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->logger = $logger;
        $this->getProductWeightSummaryUseCase = $getProductWeightSummaryUseCase;
        $this->permissionService = $permissionService;
        $this->clientRepository = $clientRepository;
    }

    #[Route('/api/weight_analytics/product_weight_summary', name: 'api_product_weight_summary', methods: ['POST'])]
    #[RequiresPermission('analytics.view')]
    public function __invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('analytics.view');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $productId = $data['productId'] ?? null;
        $from = $data['from'] ?? null;
        $to = $data['to'] ?? null;

        if (!$uuidClient || !$productId) {
            return new JsonResponse(['error' => 'uuidClient and productId are required'], 400);
        }
        
        // Verify client access - must have active subscription
        $client = $this->clientRepository->findByUuid($uuidClient);
        if (!$client) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'CLIENT_NOT_FOUND'
            ], JsonResponse::HTTP_NOT_FOUND);
        }
        
        $clientAccessCheck = $this->verifyClientAccess($client);
        if ($clientAccessCheck) {
            return $clientAccessCheck; // Returns 402 Payment Required
        }

        $dto = new GetProductWeightSummaryRequest($uuidClient, $productId, $from, $to);

        $response = $this->getProductWeightSummaryUseCase->execute($dto);

        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['summary' => $response->getSummary()], 200);
    }
}
