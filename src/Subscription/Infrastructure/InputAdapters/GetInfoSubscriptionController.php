<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Subscription\Application\DTO\GetInfoSubscriptionRequest;
use App\Subscription\Application\InputPorts\GetInfoSubscriptionUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GetInfoSubscriptionController extends AbstractController
{
    use PermissionControllerTrait;
    private GetInfoSubscriptionUseCaseInterface $useCase;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;
    private PermissionService $permissionService;

    public function __construct(GetInfoSubscriptionUseCaseInterface $useCase, SerializerInterface $serializer, LoggerInterface $logger, PermissionService $permissionService)
    {
        $this->useCase = $useCase;
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/subscriptions', name: 'api_subscriptions', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('subscription.view', 'No tienes permisos para esta acción');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $uuid = $request->query->get('uuid');
            $dto = new GetInfoSubscriptionRequest($uuid);
            $responseDto = $this->useCase->execute($dto);

            return new JsonResponse($this->serializer->serialize($responseDto, 'json'), Response::HTTP_OK, [], true);
        } catch (\Throwable $e) {
            $this->logger->error('Error fetching subscriptions', ['exception' => $e->getMessage()]);

            return new JsonResponse(['status' => 'error', 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
