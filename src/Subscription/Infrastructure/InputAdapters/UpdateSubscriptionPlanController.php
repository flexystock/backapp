<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Subscription\Application\DTO\UpdateSubscriptionPlanRequest;
use App\Subscription\Application\InputPorts\UpdateSubscriptionPlanUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

class UpdateSubscriptionPlanController extends AbstractController
{
    use PermissionControllerTrait;
    private UpdateSubscriptionPlanUseCaseInterface $updateSubscriptionPlanUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private PermissionService $permissionService;

    public function __construct(
        UpdateSubscriptionPlanUseCaseInterface $updateSubscriptionPlanUseCase,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        PermissionService $permissionService
    ) {
        $this->updateSubscriptionPlanUseCase = $updateSubscriptionPlanUseCase;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/subscription_plan_update', name: 'api_subscription_plan_update', methods: ['PUT'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('subscription.update', 'No tienes permisos para esta acción');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $dto = $this->serializer->deserialize($request->getContent(), UpdateSubscriptionPlanRequest::class, 'json');
            $dto->setUuidUserModification($this->getUser()?->getUuid());
            $dto->setDatehourModification(new \DateTime());
            $response = $this->updateSubscriptionPlanUseCase->execute($dto);

            if ($response->getError()) {
                return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
            }

            return new JsonResponse(['plan' => $response->getPlan()], $response->getStatusCode());
        } catch (AccessDeniedException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_FORBIDDEN);
        } catch (\Throwable $e) {
            $this->logger->error('Error updating subscription plan', ['exception' => $e]);

            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
