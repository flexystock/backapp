<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\UpdateScaleRequest;
use App\Scales\Application\InputPorts\UpdateScaleUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UpdateScaleController extends AbstractController
{
    private UpdateScaleUseCaseInterface $updateScaleUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;

    public function __construct(UpdateScaleUseCaseInterface $updateScaleUseCase, LoggerInterface $logger, SerializerInterface $serializer)
    {
        $this->updateScaleUseCase = $updateScaleUseCase;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    #[Route('/api/scale_update', name: 'api_scale_update', methods: ['PUT'])]
    public function __invoke(Request $request): JsonResponse
    {
        $dto = $this->serializer->deserialize($request->getContent(), UpdateScaleRequest::class, 'json');
        $dto->setUuidUserModification($this->getUser()?->getUuid());
        $dto->setDatehourModification(new \DateTime());
        $response = $this->updateScaleUseCase->execute($dto);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['scale' => $response->getScale()], $response->getStatusCode());
    }
}
