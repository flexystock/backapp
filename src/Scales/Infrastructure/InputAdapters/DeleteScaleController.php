<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\DeleteScaleRequest;
use App\Scales\Application\InputPorts\DeleteScaleUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\ClientAccessControlTrait;
use App\Security\RequiresPermission;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DeleteScaleController extends AbstractController
{
    use PermissionControllerTrait;
    use ClientAccessControlTrait;

    private DeleteScaleUseCaseInterface $deleteScaleUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        DeleteScaleUseCaseInterface $deleteScaleUseCase, 
        LoggerInterface $logger, 
        SerializerInterface $serializer,
        PermissionService $permissionService,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->deleteScaleUseCase = $deleteScaleUseCase;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->permissionService = $permissionService;
        $this->clientRepository = $clientRepository;
    }

    #[Route('/api/scale_delete', name: 'api_scale_delete', methods: ['DELETE'])]
    #[RequiresPermission('scale.delete')]
    public function __invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('scale.delete');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $dto = $this->serializer->deserialize($request->getContent(), DeleteScaleRequest::class, 'json');
        
        // Verify client access - must have active subscription
        $client = $this->clientRepository->findByUuid($dto->getUuidClient());
        if (!$client) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'CLIENT_NOT_FOUND'
            ], JsonResponse::HTTP_NOT_FOUND);
        }
        
        $clientAccessCheck = $this->verifyClientAccess($client);
        if ($clientAccessCheck) {
            return $clientAccessCheck; // Returns 402 Payment Required
        }
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'USER_NOT_AUTHENTICATED'], 401);
        }
        $response = $this->deleteScaleUseCase->execute($dto, $user);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['message' => $response->getMessage()], $response->getStatusCode());
    }
}
