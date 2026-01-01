<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Infrastructure\InputAdapters;

use App\ControlPanel\Scale\Application\DTO\GetScaleInfoRequest;
use App\ControlPanel\Scale\Application\InputPorts\GetScaleInfoUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetScaleInfoController extends AbstractController
{
    private LoggerInterface $logger;
    private GetScaleInfoUseCaseInterface $getScaleInfoUseCase;

    public function __construct(
        LoggerInterface $logger,
        GetScaleInfoUseCaseInterface $getScaleInfoUseCase
    ) {
        $this->logger = $logger;
        $this->getScaleInfoUseCase = $getScaleInfoUseCase;
    }

    #[Route('/api/control-panel/scales', name: 'api_control_panel_scales', methods: ['POST'])]
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
            $data = [];
        } else {
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['error' => 'Invalid JSON in request body'], 400);
            }
        }
        
        $endDeviceId = $data['endDeviceId'] ?? null;

        $dto = new GetScaleInfoRequest($endDeviceId);

        $response = $this->getScaleInfoUseCase->execute($dto);

        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['scales' => $response->getScalesInfo()], 200);
    }
}
