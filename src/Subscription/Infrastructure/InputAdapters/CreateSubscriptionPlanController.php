<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Subscription\Application\DTO\CreateSubscriptionPlanRequest;
use App\Subscription\Application\InputPorts\CreateSubscriptionPlanUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateSubscriptionPlanController extends AbstractController
{
    private CreateSubscriptionPlanUseCaseInterface $createSubscriptionPlanUseCase;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(
        CreateSubscriptionPlanUseCaseInterface $createSubscriptionPlanUseCase,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ) {
        $this->createSubscriptionPlanUseCase = $createSubscriptionPlanUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    #[Route('/api/create_subscription_plan', name: 'api_create_subscription_plan', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            if (!$this->isGranted('ROLE_ROOT')) {
                throw $this->createAccessDeniedException('No tienes permiso.');
            }

            $createSubscriptionPlanRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateSubscriptionPlanRequest::class,
                'json'
            );

            $user = $this->getUser();
            // 4) Asignar el userCreation
            $uuidUser = $user->getUuid();
            $createSubscriptionPlanRequest->setUuidUser($uuidUser);

            $errors = $this->validator->validate($createSubscriptionPlanRequest);
            if ($errors->count() > 0) {
                $this->logger->warning('Validación fallida al crear plan de suscripción', [
                    'errors' => (string) $errors,
                ]);

                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Validación fallida',
                    'errors' => json_decode($this->serializer->serialize($errors, 'json'), true),
                ], Response::HTTP_BAD_REQUEST);
            }

            $responseDto = $this->createSubscriptionPlanUseCase->execute($createSubscriptionPlanRequest);

            return new JsonResponse([
                'status' => 'success',
                'plan' => $responseDto->getPlan(),
            ], Response::HTTP_CREATED);
        } catch (\RuntimeException $e) {
            if ('PLAN_ALREADY_EXISTS' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'PLAN_ALREADY_EXISTS',
                ], Response::HTTP_CONFLICT);
            }

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            $this->logger->error('Error al crear plan de suscripción', [
                'exception' => $e->getMessage(),
            ]);

            return new JsonResponse([
                'status' => 'error',
                'message' => 'Error interno del servidor',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
