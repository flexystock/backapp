<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Infrastructure\InputAdapters;

use App\ControlPanel\Purchase\Application\DTO\ProcessPurchaseScalesRequest;
use App\ControlPanel\Purchase\Application\InputPorts\ProcessPurchaseScalesUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/api/control-panel/purchases/process', name: 'control_panel_process_purchase_scales', methods: ['POST'])]
class ProcessPurchaseScalesController extends AbstractController
{
    private ProcessPurchaseScalesUseCaseInterface $useCase;
    private Security $security;

    public function __construct(
        ProcessPurchaseScalesUseCaseInterface $useCase,
        Security $security
    ) {
        $this->useCase = $useCase;
        $this->security = $security;
    }

    public function __invoke(Request $request): JsonResponse
    {
        // Check if user has ROOT role
        if (!$this->isGranted('ROLE_ROOT')) {
            return new JsonResponse(
                ['error' => 'Access denied. ROOT role required.'],
                Response::HTTP_FORBIDDEN
            );
        }

        // Get authenticated user
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(
                ['error' => 'User not authenticated'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Parse JSON content
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
                ['error' => 'Invalid JSON: ' . json_last_error_msg()],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Validate required fields
        if (!isset($data['uuidPurchase'])) {
            return new JsonResponse(
                ['error' => 'Missing required field: uuidPurchase'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Get user UUID
        $uuidUser = method_exists($user, 'getUuid') ? $user->getUuid() : null;
        
        if (!$uuidUser) {
            return new JsonResponse(
                ['error' => 'Unable to retrieve user UUID'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // Create request DTO
        $requestDto = new ProcessPurchaseScalesRequest(
            $data['uuidPurchase'],
            $uuidUser
        );

        // Execute use case
        $response = $this->useCase->execute($requestDto);

        // Return response
        return new JsonResponse(
            $response->toArray(),
            $response->isSuccess() ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }
}
