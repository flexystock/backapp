<?php

namespace App\User\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\User\Application\DTO\Profile\GetUserInfoRequest;
use App\User\Application\InputPorts\Profile\GetUserInfoUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetUserInfoController extends AbstractController
{
    use PermissionControllerTrait;

    private LoggerInterface $logger;
    private GetUserInfoUseCaseInterface $getUserInfoUseCase;

    public function __construct(
        LoggerInterface $logger,
        GetUserInfoUseCaseInterface $getUserInfoUseCase,
        PermissionService $permissionService
    ) {
        $this->logger = $logger;
        $this->getUserInfoUseCase = $getUserInfoUseCase;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/user/get_info', name: 'api_user_info', methods: ['POST'])]
    #[RequiresPermission('user.view')]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $requestedUuidUser = $data['uuidUser'] ?? null;
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not authenticated'], 401);
        }

        if (!$uuidClient) {
            return new JsonResponse(['error' => 'uuidClient is required'], 400);
        }

        // Allow users to view their own info without permission check
        // If requesting another user's info, check permission
        if ($requestedUuidUser && $requestedUuidUser !== $user->getUuid()) {
            $permissionCheck = $this->checkPermissionJson('user.view');
            if ($permissionCheck) {
                return $permissionCheck;
            }
        }

        $uuidUser = $requestedUuidUser ?? $user->getUuid();
        $dto = new GetUserInfoRequest($uuidClient, $uuidUser);

        $response = $this->getUserInfoUseCase->execute($dto);
        if ($response->getError()) {
            return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
        }

        return new JsonResponse(['user_info' => $response->getUserInfo()], 200);
    }
}
