<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\DeleteScaleRequest;
use App\Scales\Application\InputPorts\DeleteScaleUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DeleteScaleController extends AbstractController
{
    private DeleteScaleUseCaseInterface $deleteScaleUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;

    public function __construct(DeleteScaleUseCaseInterface $deleteScaleUseCase, LoggerInterface $logger, SerializerInterface $serializer)
    {
        $this->deleteScaleUseCase = $deleteScaleUseCase;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    #[Route('/api/scale_delete', name: 'api_scale_delete', methods: ['DELETE'])]
    public function __invoke(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), DeleteScaleRequest::class, 'json');
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'USER_NOT_AUTHENTICATED'], 401);
        }
        $response = $this->deleteScaleUseCase->execute($dto, $user);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['message' => $response->getMessage()], $response->getStatusCode());
    }
}
