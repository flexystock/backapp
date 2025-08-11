<?php

namespace App\Subscription\Infrastructure\InputAdapters;

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
    private DeleteSubscriptionUseCaseInterface $useCase;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;

    public function __construct(DeleteSubscriptionUseCaseInterface $useCase, SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->useCase = $useCase;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    #[Route('/api/subscription_delete', name: 'api_subscription_delete', methods: ['DELETE'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            if (!$this->isGranted('ROLE_ROOT')) {
                throw $this->createAccessDeniedException('No tienes permiso.');
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
