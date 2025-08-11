<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\UnassignScaleFromProductRequest;
use App\Scales\Application\InputPorts\UnassignScaleFromProductUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UnassignScaleFromProduct extends AbstractController
{
    private LoggerInterface $logger;
    private UnassignScaleFromProductUseCaseInterface $unassignScaleFromProductUseCase;

    public function __construct(
        LoggerInterface $logger,
        UnassignScaleFromProductUseCaseInterface $unassignScaleFromProductUseCase
    ) {
        $this->logger = $logger;
        $this->unassignScaleFromProductUseCase = $unassignScaleFromProductUseCase;
    }

    #[Route('/api/unassign_scale_product', name: 'api_unassign_scales', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $endDeviceId = $data['end_device_id'] ?? null;

        if (!$uuidClient || !$endDeviceId) {
            return new JsonResponse(['error' => 'uuidClient are required'], 400);
        }
        $uuidUser = $this->getUser()->getUuid();
        if (!$uuidUser) {
            return new JsonResponse(['error' => 'uuidUser are required'], 400);
        }
        $dto = new UnassignScaleFromProductRequest($uuidClient, $endDeviceId, $uuidUser);

        $response = $this->unassignScaleFromProductUseCase->execute($dto);

        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['scale' => $response->getScale()], $response->getStatusCode());
    }
}
