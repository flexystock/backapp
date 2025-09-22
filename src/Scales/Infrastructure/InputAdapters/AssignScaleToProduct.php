<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\AssignScaleToProductRequest;
use App\Scales\Application\InputPorts\AssignScaleToProductUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssignScaleToProduct extends AbstractController
{
    use PermissionControllerTrait;
    private LoggerInterface $logger;
    private AssignScaleToProductUseCaseInterface $assignScaleToProductUseCase;

    public function __construct(
        LoggerInterface $logger,
        AssignScaleToProductUseCaseInterface $assignScaleToProductUseCase,
        PermissionService $permissionService
    ) {
        $this->logger = $logger;
        $this->assignScaleToProductUseCase = $assignScaleToProductUseCase;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/assign_scale_product', name: 'api_assign_scales', methods: ['POST'])]
    #[RequiresPermission('scale.assign')]
    public function __invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('scale.assign', 'No tiene permiso para asignar balanzas a productos');
        if ($permissionCheck) {
            return $permissionCheck;
        }

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
