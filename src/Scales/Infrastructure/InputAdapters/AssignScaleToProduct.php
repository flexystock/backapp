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

    #[Route('/api/assign_sacale_product', name: 'api_assig_scales', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        if (!$uuidClient) {
            return new JsonResponse(['error' => 'uuidClient is required'], 400);
        }

        $dto = new AssignScaleToProductRequest($uuidClient, $data);

        $response = $this->assignScaleToProductUseCase->execute($dto);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }
        return new JsonResponse(['scale' => $response->getScale()], $response->getStatusCode());
    }
}
