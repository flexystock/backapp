<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Infrastructure\InputAdapters;

use App\ControlPanel\Scale\Application\DTO\ActivateDeactivateScaleRequest;
use App\ControlPanel\Scale\Application\InputPorts\ActivateDeactivateScaleUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActivateDeactivateScaleController extends AbstractController
{
    private LoggerInterface $logger;
    private ActivateDeactivateScaleUseCaseInterface $activateDeactivateScaleUseCase;

    public function __construct(
        LoggerInterface $logger,
        ActivateDeactivateScaleUseCaseInterface $activateDeactivateScaleUseCase
    ) {
        $this->logger = $logger;
        $this->activateDeactivateScaleUseCase = $activateDeactivateScaleUseCase;
    }

    #[Route('/api/control-panel/scales/activate-deactivate', name: 'api_control_panel_scales_activate_deactivate', methods: ['POST'])]
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
        
        if (empty($content)) {
            return new JsonResponse(['error' => 'Request body is required'], 400);
        }

        $data = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON in request body'], 400);
        }

        // Validate required fields
        if (!isset($data['client_name'])) {
            return new JsonResponse(['error' => 'client_name is required'], 400);
        }

        if (!isset($data['end_device_id'])) {
            return new JsonResponse(['error' => 'end_device_id is required'], 400);
        }

        if (!isset($data['active'])) {
            return new JsonResponse(['error' => 'active is required'], 400);
        }

        $clientName = $data['client_name'];
        $endDeviceId = $data['end_device_id'];
        $active = (bool) $data['active'];

        $dto = new ActivateDeactivateScaleRequest($clientName, $endDeviceId, $active);

        $response = $this->activateDeactivateScaleUseCase->execute($dto);

        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['message' => $response->getMessage()], 200);
    }
}
