<?php

declare(strict_types=1);

namespace App\Admin\Infrastructure\InputAdapters;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use App\Entity\Main\User;

class DashboardController extends AbstractController
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/api/admin', name: 'admin_dashboard', methods: ['GET'])]
    public function dashboard(): JsonResponse
    {
        if (!$this->isGranted('ROLE_ROOT')) {
            throw $this->createAccessDeniedException('No tienes permiso.');
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
