<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\AssignScaleToProductRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Scales\Application\InputPorts\AssignScaleToProductUseCaseInterface;

class AssignScaleToProduct extends AbstractController
{
    private LoggerInterface $logger;
    private AssignScaleToProductUseCaseInterface $assignScaleToProductUseCase;

    public function __construct(
        LoggerInterface $logger,
        AssignScaleToProductUseCaseInterface $assignScaleToProductUseCase
    ) {
        $this->logger = $logger;
        $this->assignScaleToProductUseCase = $assignScaleToProductUseCase;
    }

    #[Route('/api/assign_sacale_product', name: 'api_assign_scales', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $endDeviceId = $data['end_device_id'] ?? null;
        $productId = $data['productId'] ?? null;

        if (!$uuidClient || !$endDeviceId || null === $productId) {
            return new JsonResponse(['error' => 'INVALID_DATA'], 400);
        }
        $uuidUser = $this->getUser()->getUuid();
        if (!$uuidUser) {
            return new JsonResponse(['error' => 'USER_NOT_FOUND'], 400);
        }
        $dto = new AssignScaleToProductRequest($uuidClient, $endDeviceId, (int) $productId, $uuidUser);

        $response = $this->assignScaleToProductUseCase->execute($dto);

        if (!$response->isSuccess()) {
            return new JsonResponse(['error' => $response->getError()], 400);
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
