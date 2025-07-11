<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Subscription\Application\DTO\CreateSubscriptionPlanRequest;
use App\Subscription\Application\DTO\CreateSubscriptionPlanResponse;
use App\Subscription\Application\UseCases\CreateSubscriptionPlanUseCase;
use App\Subscription\Application\OuputPorts\SubscriptionPlanRepositoryInterface;
use App\Subscription\Application\InputPorts\CreateSubscriptionPlanUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CreateSubscriptionPlanController extends AbstractController
{
    private CreateSubscriptionPlanUseCaseInterface $createSubscriptionPlanUseCase;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(
        CreateSubscriptionPlanUseCaseInterface $createSubscriptionPlanUseCase,
        SerializerInterface                    $serializer,
        ValidatorInterface                     $validator,
        LoggerInterface                        $logger
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

            $errors = $this->validator->validate($createSubscriptionPlanRequest);
            if ($errors->count() > 0) {
                $this->logger->warning('Validación fallida al crear plan de suscripción', [
                    'errors' => (string)$errors
                ]);
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Validación fallida',
                    'errors' => json_decode($this->serializer->serialize($errors, 'json'), true)
                ], Response::HTTP_BAD_REQUEST);
            }

            $response = $this->createSubscriptionPlanUseCase->execute($createSubscriptionPlanRequest);

            return new JsonResponse([
                'status' => 'success',
                'data' => json_decode($this->serializer->serialize($response, 'json'), true)
            ], Response::HTTP_CREATED);

        } catch (\Throwable $e) {
            $this->logger->error('Error al crear plan de suscripción', [
                'exception' => $e->getMessage()
            ]);
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Error interno del servidor'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
