<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Scales\Application\DTO\GetAllScalesRequest;
use App\Scales\Application\InputPorts\GetAllScalesUseCaseInterface;
use App\Security\ClientAccessControlTrait;
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
    use ClientAccessControlTrait;

    private GetAllScalesUseCaseInterface $getAllScalesUseCase;
    private LoggerInterface $logger;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        GetAllScalesUseCaseInterface $getAllScalesUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->getAllScalesUseCase = $getAllScalesUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
        $this->clientRepository = $clientRepository;
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

        // Verify client access - must have active subscription
        $client = $this->clientRepository->findByUuid($uuidClient);
        if (!$client) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'CLIENT_NOT_FOUND',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $clientAccessCheck = $this->verifyClientAccess($client);
        if ($clientAccessCheck) {
            return $clientAccessCheck; // Returns 402 Payment Required
        }
        $dto = new GetAllScalesRequest($uuidClient);
        $response = $this->getAllScalesUseCase->execute($dto);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['scale' => $response->getScale()], $response->getStatusCode());
    }
}
