<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Scales\Application\DTO\UpdateScaleRequest;
use App\Scales\Application\InputPorts\UpdateScaleUseCaseInterface;
use App\Security\ClientAccessControlTrait;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UpdateScaleController extends AbstractController
{
    use PermissionControllerTrait;
    use ClientAccessControlTrait;

    private UpdateScaleUseCaseInterface $updateScaleUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        UpdateScaleUseCaseInterface $updateScaleUseCase,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        PermissionService $permissionService,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->updateScaleUseCase = $updateScaleUseCase;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->permissionService = $permissionService;
        $this->clientRepository = $clientRepository;
    }

    #[Route('/api/scale_update', name: 'api_scale_update', methods: ['PUT'])]
    #[RequiresPermission('scale.update')]
    public function __invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('scale.update');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $dto = $this->serializer->deserialize($request->getContent(), UpdateScaleRequest::class, 'json');

        // Verify client access - must have active subscription
        $client = $this->clientRepository->findByUuid($dto->getUuidClient());
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
        $dto->setUuidUserModification($this->getUser()?->getUuid());
        $dto->setDatehourModification(new \DateTime());
        $response = $this->updateScaleUseCase->execute($dto);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['scale' => $response->getScale()], $response->getStatusCode());
    }
}
