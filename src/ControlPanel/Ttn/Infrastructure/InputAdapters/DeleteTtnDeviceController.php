<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Infrastructure\InputAdapters;

use App\ControlPanel\Ttn\Application\DTO\DeleteTtnDeviceRequest;
use App\ControlPanel\Ttn\Application\InputPorts\DeleteTtnDeviceUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DeleteTtnDeviceController extends AbstractController
{
    private LoggerInterface $logger;
    private DeleteTtnDeviceUseCaseInterface $deleteTtnDeviceUseCase;

    public function __construct(
        LoggerInterface $logger,
        DeleteTtnDeviceUseCaseInterface $deleteTtnDeviceUseCase
    ) {
        $this->logger = $logger;
        $this->deleteTtnDeviceUseCase = $deleteTtnDeviceUseCase;
    }

    #[Route('/api/control-panel/ttn/device', name: 'api_control_panel_delete_ttn_device', methods: ['DELETE'])]
    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        // Only ROOT users can access this endpoint
        if (!in_array('ROLE_ROOT', $user->getRoles())) {
            return new JsonResponse(['error' => 'Access denied. ROOT role required.'], 403);
        }

        $content = $request->getContent();

        // Handle empty content
        if (empty($content)) {
            return new JsonResponse(['error' => 'Request body is required'], 400);
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON in request body'], 400);
        }

        $endDeviceId = $data['endDeviceId'] ?? null;

        if (!$endDeviceId) {
            return new JsonResponse(['error' => 'endDeviceId is required'], 400);
        }

        $dto = new DeleteTtnDeviceRequest($endDeviceId);

        $response = $this->deleteTtnDeviceUseCase->execute($dto);

        return new JsonResponse([
            'success' => $response->isSuccess(),
            'message' => $response->getMessage(),
        ], $response->getStatusCode());
    }
}
