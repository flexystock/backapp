<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\Subscription\Application\InputPorts\GetInfoSubscriptionPlansUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GetInfoSubscriptionPlansController extends AbstractController
{
    use PermissionControllerTrait;

    private GetInfoSubscriptionPlansUseCaseInterface $getInfoSubscriptionPlansUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;

    public function __construct(
        GetInfoSubscriptionPlansUseCaseInterface $getInfoSubscriptionPlansUseCase,
        LoggerInterface $logger,
        SerializerInterface $serializer,
        PermissionService $permissionService
    ) {
        $this->getInfoSubscriptionPlansUseCase = $getInfoSubscriptionPlansUseCase;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/subscription_plans', name: 'api_subscription_plans', methods: ['GET'])]
    #[RequiresPermission('subscription.view')]
    public function __invoke(): JsonResponse
    {
        try {
            // Check permission using new system
            $permissionCheck = $this->checkPermissionJson('subscription.view', 'No tienes permisos para ver planes de suscripción');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            // Si tu UseCase requiere un request DTO, puedes crear uno vacío o eliminar el parámetro.
            $responseDto = $this->getInfoSubscriptionPlansUseCase->execute();

            return new JsonResponse(
                $this->serializer->serialize($responseDto, 'json'),
                Response::HTTP_OK,
                [],
                true
            );
        } catch (\RuntimeException $e) {
            if ('PLAN_NOT_FOUND' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'PLAN_NOT_FOUND',
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al obtener planes de suscripción', [
                'exception' => $e->getMessage(),
            ]);

            return new JsonResponse([
                'status' => 'error',
                'message' => 'Error interno del servidor',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
