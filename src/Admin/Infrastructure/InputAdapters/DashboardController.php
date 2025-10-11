<?php

declare(strict_types=1);

namespace App\Admin\Infrastructure\InputAdapters;

use App\Entity\Main\User;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    use PermissionControllerTrait;

    private UserRepositoryInterface $userRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PermissionService $permissionService
    ) {
        $this->userRepository = $userRepository;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/admin', name: 'admin_dashboard', methods: ['GET'])]
    #[RequiresPermission('analytics.view')]
    public function dashboard(): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('analytics.view');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $users = $this->userRepository->findAll();

        $usersData = array_map(
            static function (User $user): array {
                return [
                    'uuid' => $user->getUuid(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                ];
            },
            $users
        );

        return new JsonResponse(['users' => $usersData]);
    }
}
