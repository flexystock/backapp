<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Subscription\Application\DTO\UpdateSubscriptionRequest;
use App\Subscription\Application\InputPorts\UpdateSubscriptionUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class UpdateSubscriptionController extends AbstractController
{
    private UpdateSubscriptionUseCaseInterface $useCase;
    private SerializerInterface $serializer;
    private LoggerInterface $logger;

    public function __construct(UpdateSubscriptionUseCaseInterface $useCase, SerializerInterface $serializer, LoggerInterface $logger)
    {
        $this->useCase = $useCase;
        $this->serializer = $serializer;
        $this->logger = $logger;
    }

    #[Route('/api/subscription_update', name: 'api_subscription_update', methods: ['PUT'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            if (!$this->isGranted('ROLE_ROOT')) {
                throw $this->createAccessDeniedException('No tienes permiso.');
            }

            $dto = $this->serializer->deserialize($request->getContent(), UpdateSubscriptionRequest::class, 'json');
            $responseDto = $this->useCase->execute($dto);

            if ($responseDto->getError()) {
                return new JsonResponse(['error' => $responseDto->getError()], $responseDto->getStatusCode());
            }

            return new JsonResponse(['subscription' => $responseDto->getSubscription()], $responseDto->getStatusCode());
        } catch (\Throwable $e) {
            $this->logger->error('Error updating subscription', ['exception' => $e->getMessage()]);
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
