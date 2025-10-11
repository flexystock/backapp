<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\GetAllScalesRequest;
use App\Scales\Application\InputPorts\GetAllScalesUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetAllScalesController extends AbstractController
{
    use PermissionControllerTrait;

    private GetAllScalesUseCaseInterface $getAllScalesUseCase;
    private LoggerInterface $logger;

    public function __construct(
        GetAllScalesUseCaseInterface $getAllScalesUseCase, 
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->getAllScalesUseCase = $getAllScalesUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/scales', name: 'api_scales', methods: ['POST'])]
    #[RequiresPermission('scale.view')]
    public function __invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('scale.view');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        if (!$uuidClient) {
            return new JsonResponse(['error' => 'uuidClient is required'], 400);
        }
        $dto = new GetAllScalesRequest($uuidClient);
        $response = $this->getAllScalesUseCase->execute($dto);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['scale' => $response->getScale()], $response->getStatusCode());
    }
}
