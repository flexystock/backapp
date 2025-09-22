<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\GetScaleRequest;
use App\Scales\Application\InputPorts\GetScaleUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetScaleController extends AbstractController
{
    use PermissionControllerTrait;

    private GetScaleUseCaseInterface $getScaleUseCase;
    private LoggerInterface $logger;

    public function __construct(
        GetScaleUseCaseInterface $getScaleUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->getScaleUseCase = $getScaleUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/scale', name: 'api_scale', methods: ['POST'])]
    #[RequiresPermission('scale.view')]
    public function __invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('scale.view');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $uuidScale = $data['uuid'] ?? null;

        if (!$uuidClient || !$uuidScale) {
            return new JsonResponse(['error' => 'uuidClient and uuid are required'], 400);
        }

        $dto = new GetScaleRequest($uuidClient, $uuidScale);
        $response = $this->getScaleUseCase->execute($dto);

        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['scale' => $response->getScale()], $response->getStatusCode());
    }
}
