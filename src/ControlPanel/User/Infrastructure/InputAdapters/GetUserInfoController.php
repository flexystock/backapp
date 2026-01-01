<?php

declare(strict_types=1);

namespace App\ControlPanel\User\Infrastructure\InputAdapters;

use App\ControlPanel\User\Application\DTO\GetUserInfoRequest;
use App\ControlPanel\User\Application\InputPorts\GetUserInfoUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetUserInfoController extends AbstractController
{
    private LoggerInterface $logger;
    private GetUserInfoUseCaseInterface $getUserInfoUseCase;

    public function __construct(
        LoggerInterface $logger,
        GetUserInfoUseCaseInterface $getUserInfoUseCase
    ) {
        $this->logger = $logger;
        $this->getUserInfoUseCase = $getUserInfoUseCase;
    }

    #[Route('/api/control-panel/users', name: 'api_control_panel_users', methods: ['POST'])]
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
            $data = [];
        } else {
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['error' => 'Invalid JSON in request body'], 400);
            }
        }

        $emailUser = $data['email'] ?? null;

        $dto = new GetUserInfoRequest($emailUser);

        $response = $this->getUserInfoUseCase->execute($dto);

        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['users' => $response->getUsersInfo()], 200);
    }
}
