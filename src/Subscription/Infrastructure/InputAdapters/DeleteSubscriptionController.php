<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Subscription\Application\DTO\DeleteSubscriptionRequest;
use App\Subscription\Application\InputPorts\DeleteSubscriptionUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DeleteSubscriptionController extends AbstractController
{
    use PermissionControllerTrait;
    private DeleteSubscriptionUseCaseInterface $useCase;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;
    private PermissionService $permissionService;

    public function __construct(DeleteSubscriptionUseCaseInterface $useCase, SerializerInterface $serializer, LoggerInterface $logger, PermissionService $permissionService)
    {
        $this->useCase = $useCase;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/subscription_delete', name: 'api_subscription_delete', methods: ['DELETE'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('subscription.delete', 'No tienes permisos para esta acción');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $dto = $this->serializer->deserialize($request->getContent(), DeleteSubscriptionRequest::class, 'json');
            $responseDto = $this->useCase->execute($dto);

            if ($responseDto->getError()) {
                return new JsonResponse(['error' => $responseDto->getError()], $responseDto->getStatusCode());
            }

            return new JsonResponse(['message' => $responseDto->getMessage()], $responseDto->getStatusCode());
        } catch (\Throwable $e) {
            $this->logger->error('Error deleting subscription', ['exception' => $e->getMessage()]);

            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
