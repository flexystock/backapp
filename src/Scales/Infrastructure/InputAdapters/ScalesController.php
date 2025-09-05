<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\RegisterScalesRequest;
use App\Scales\Application\InputPorts\RegisterScalesUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScalesController extends AbstractController
{
    use PermissionControllerTrait;

    private RegisterScalesUseCaseInterface $registerScalesUseCase;
    private LoggerInterface $logger;

    public function __construct(
        RegisterScalesUseCaseInterface $registerScalesUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->registerScalesUseCase = $registerScalesUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/scale_register', name: 'scale_register', methods: ['POST'])]
    #[RequiresPermission('scale.create')]
    public function registerScale(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('scale.create');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        // 1. parsear JSON
        $data = json_decode($request->getContent(), true);

        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $uuidClient = $data['uuidClient'] ?? null;
        $endDeviceId = $data['endDeviceId'] ?? null;
        $voltageMin = $data['voltage_min'] ?? null;  // si lo pasas
        $uuidUser = $this->getUser() ? $this->getUser()->getUuid() : 'system';

        if (!$uuidClient || !$endDeviceId) {
            $this->logger->warning('ScalesController: Missing required fields');

            return new JsonResponse(['error' => 'uuidClient and endDeviceId are required'], 400);
        }

        // 2. crear DTO
        $dto = new RegisterScalesRequest(
            $uuidClient,
            $endDeviceId,
            $voltageMin ? floatval($voltageMin) : null,
            $uuidUser
        );

        // 3. ejecutar caso de uso
        $response = $this->registerScalesUseCase->execute($dto);

        // 4. retornar respuesta
        if ($response->isSuccess()) {
            return new JsonResponse(['message' => 'Scale registered successfully'], 200);
        } else {
            return new JsonResponse(['error' => $response->getError()], 400);
        }
    }
}
