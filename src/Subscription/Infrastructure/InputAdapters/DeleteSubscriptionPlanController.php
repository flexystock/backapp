<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Subscription\Application\DTO\DeleteSubscriptionPlanRequest;
use App\Subscription\Application\InputPorts\DeleteSubscriptionPlanUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class DeleteSubscriptionPlanController extends AbstractController
{
    use PermissionControllerTrait;
    private DeleteSubscriptionPlanUseCaseInterface $deleteSubscriptionPlanUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private PermissionService $permissionService;

    public function __construct(DeleteSubscriptionPlanUseCaseInterface $deleteSubscriptionPlanUseCase, LoggerInterface $logger, SerializerInterface $serializer, PermissionService $permissionService)
    {
        $this->deleteSubscriptionPlanUseCase = $deleteSubscriptionPlanUseCase;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/subscription_plan_delete', name: 'api_subscription_plan_delete', methods: ['DELETE'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('subscription.delete', 'No tienes permisos para esta acción');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $dto = $this->serializer->deserialize($request->getContent(), DeleteSubscriptionPlanRequest::class, 'json');
            $response = $this->deleteSubscriptionPlanUseCase->execute($dto);

            if ($response->getError()) {
                return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
            }

            return new JsonResponse(['message' => $response->getMessage()], $response->getStatusCode());
        } catch (AccessDeniedException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            $this->logger->error('Error deleting subscription plan', ['exception' => $e]);

            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
