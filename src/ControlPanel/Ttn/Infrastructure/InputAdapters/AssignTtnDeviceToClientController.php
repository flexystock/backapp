<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Infrastructure\InputAdapters;

use App\ControlPanel\Ttn\Application\DTO\AssignTtnDeviceToClientRequest;
use App\ControlPanel\Ttn\Application\InputPorts\AssignTtnDeviceToClientUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssignTtnDeviceToClientController extends AbstractController
{
    private LoggerInterface $logger;
    private AssignTtnDeviceToClientUseCaseInterface $assignTtnDeviceToClientUseCase;

    public function __construct(
        LoggerInterface $logger,
        AssignTtnDeviceToClientUseCaseInterface $assignTtnDeviceToClientUseCase
    ) {
        $this->logger = $logger;
        $this->assignTtnDeviceToClientUseCase = $assignTtnDeviceToClientUseCase;
    }

    #[Route('/api/control-panel/ttn/assign-device', name: 'api_control_panel_assign_ttn_device', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        // Only ROOT users can access this endpoint
        if (!$this->isGranted('ROLE_ROOT')) {
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
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$endDeviceId) {
            return new JsonResponse(['error' => 'endDeviceId is required'], 400);
        }

        if (!$uuidClient) {
            return new JsonResponse(['error' => 'uuidClient is required'], 400);
        }

        $dto = new AssignTtnDeviceToClientRequest($endDeviceId, $uuidClient);

        $response = $this->assignTtnDeviceToClientUseCase->execute($dto);

        return new JsonResponse([
            'success' => $response->isSuccess(),
            'message' => $response->getMessage(),
        ], $response->getStatusCode());
    }
}
