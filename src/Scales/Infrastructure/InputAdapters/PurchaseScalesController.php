<?php

declare(strict_types=1);

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\PurchaseScalesRequest;
use App\Scales\Application\InputPorts\PurchaseScalesUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/scales/purchase', name: 'scales_purchase', methods: ['POST'])]
class PurchaseScalesController extends AbstractController
{
    private PurchaseScalesUseCaseInterface $purchaseScalesUseCase;
    private LoggerInterface $logger;

    public function __construct(
        PurchaseScalesUseCaseInterface $purchaseScalesUseCase,
        LoggerInterface $logger
    ) {
        $this->purchaseScalesUseCase = $purchaseScalesUseCase;
        $this->logger = $logger;
    }

    public function __invoke(Request $request): JsonResponse
    {
        // Validate user is authenticated
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(
                ['error' => 'Unauthorized'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Get and validate JSON content
        $content = $request->getContent();
        if (empty($content)) {
            return new JsonResponse(
                ['error' => 'Empty request body'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(
                ['error' => 'Invalid JSON format'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Validate required fields
        if (!isset($data['uuidClient']) || !isset($data['numScales'])) {
            return new JsonResponse(
                ['error' => 'Missing required fields: uuidClient and numScales'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (!is_int($data['numScales']) || $data['numScales'] < 1) {
            return new JsonResponse(
                ['error' => 'numScales must be a positive integer'],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $purchaseRequest = new PurchaseScalesRequest(
                $data['uuidClient'],
                $data['numScales']
            );

            $response = $this->purchaseScalesUseCase->execute($purchaseRequest);

            return new JsonResponse(
                $response->toArray(),
                $response->getStatusCode()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error in PurchaseScalesController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return new JsonResponse(
                ['error' => 'Internal server error'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
