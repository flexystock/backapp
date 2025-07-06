<?php

namespace App\User\Infrastructure\InputAdapters;

use App\User\Application\DTO\Profile\GetUserInfoRequest;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\User\Application\InputPorts\Profile\GetUserInfoUseCaseInterface;
class GetUserInfoController extends AbstractController
{
    private LoggerInterface $logger;
    private GetUserInfoUseCaseInterface $getUserInfoUseCase;

    public function __construct(
        LoggerInterface $logger,
        GetUserInfoUseCaseInterface $getUserInfoUseCase
    ) {
        $this->logger = $logger;
        $this->getUserInfoUseCase = $getUserInfoUseCase;
    }

    #[Route('/api/user/get_info', name: 'api_user_info', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        if (!$uuidClient) {
            return new JsonResponse(['error' => 'uuidClient is required'], 400);
        }

        $uuidUser = $user->getUuid();
        $dto = new GetUserInfoRequest($uuidClient, $uuidUser);

        $response = $this->getUserInfoUseCase->execute($dto);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }
        return new JsonResponse(['user_info' => $response->getUserInfo()], 200);
    }

}