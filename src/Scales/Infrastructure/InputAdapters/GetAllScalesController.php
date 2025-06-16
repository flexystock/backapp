<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\GetAllScalesRequest;
use App\Scales\Application\InputPorts\GetAllScalesUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetAllScalesController extends AbstractController
{
    private GetAllScalesUseCaseInterface $getAllScalesUseCase;
    private LoggerInterface $logger;

    public function __construct(GetAllScalesUseCaseInterface $getAllScalesUseCase, LoggerInterface $logger)
    {
        $this->getAllScalesUseCase = $getAllScalesUseCase;
        $this->logger = $logger;
    }

    #[Route('/api/scales', name: 'api_scales', methods: ['POST'])]
    #[IsGranted('PERMISSION_scales')]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        if (!$uuidClient) {
            return new JsonResponse(['error' => 'uuidClient is required'], 400);
        }
        $dto = new GetAllScalesRequest($uuidClient);
        $response = $this->getAllScalesUseCase->execute($dto);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }
        return new JsonResponse(['scale' => $response->getScale()], $response->getStatusCode());
    }
}
