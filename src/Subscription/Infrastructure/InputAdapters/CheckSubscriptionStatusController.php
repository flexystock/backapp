<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Subscription\Application\DTO\CheckSubscriptionStatusRequest;
use App\Subscription\Application\InputPorts\CheckSubscriptionStatusUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckSubscriptionStatusController extends AbstractController
{
    use PermissionControllerTrait;

    private CheckSubscriptionStatusUseCaseInterface $checkSubscriptionStatusUseCase;
    private LoggerInterface $logger;
    private PermissionService $permissionService;

    public function __construct(
        CheckSubscriptionStatusUseCaseInterface $checkSubscriptionStatusUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->checkSubscriptionStatusUseCase = $checkSubscriptionStatusUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/subscription/status', name: 'api_subscription_status_check', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('subscription.view', 'No tienes permisos para esta acción');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $clientUuid = $request->query->get('client_uuid');
            $subscriptionUuid = $request->query->get('subscription_uuid');

            if (!$clientUuid && !$subscriptionUuid) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Se requiere client_uuid o subscription_uuid como parámetro',
                ], Response::HTTP_BAD_REQUEST);
            }

            $dto = new CheckSubscriptionStatusRequest($clientUuid, $subscriptionUuid);
            $responseDto = $this->checkSubscriptionStatusUseCase->execute($dto);

            if ($responseDto->getError()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $responseDto->getError(),
                ], $responseDto->getStatusCode());
            }

            return new JsonResponse([
                'status' => 'success',
                'data' => $responseDto->getData(),
            ], Response::HTTP_OK);

        } catch (\Throwable $e) {
            $this->logger->error('Error checking subscription status', [
                'exception' => $e->getMessage(),
                'client_uuid' => $request->query->get('client_uuid'),
                'subscription_uuid' => $request->query->get('subscription_uuid')
            ]);

            return new JsonResponse([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}