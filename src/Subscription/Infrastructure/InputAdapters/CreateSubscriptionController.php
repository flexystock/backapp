<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Subscription\Application\DTO\CreateSubscriptionRequest;
use App\Subscription\Application\InputPorts\CreateSubscriptionUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class CreateSubscriptionController extends AbstractController
{
    private CreateSubscriptionUseCaseInterface $createSubscriptionUseCase;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(CreateSubscriptionUseCaseInterface $createSubscriptionUseCase, SerializerInterface $serializer, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->createSubscriptionUseCase = $createSubscriptionUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    #[Route('/api/create_subscription', name: 'api_create_subscription', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            if (!$this->isGranted('ROLE_ROOT')) {
                throw $this->createAccessDeniedException('No tienes permiso.');
            }

            $dto = $this->serializer->deserialize($request->getContent(), CreateSubscriptionRequest::class, 'json');
            $errors = $this->validator->validate($dto);
            if ($errors->count() > 0) {
                return new JsonResponse([
                    'status' => 'error',
                    'errors' => json_decode($this->serializer->serialize($errors, 'json'), true)
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            $uuidUser = $user->getUuid();
            $dto->setUuidUser($uuidUser);

            $responseDto = $this->createSubscriptionUseCase->execute($dto);

            if ($responseDto->getError()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $responseDto->getError()
                ], $responseDto->getStatusCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return new JsonResponse([
                'status' => 'success',
                'subscription' => $responseDto->getSubscription()
            ], Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            $this->logger->error('Error creating subscription', ['exception' => $e->getMessage()]);
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Internal Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
