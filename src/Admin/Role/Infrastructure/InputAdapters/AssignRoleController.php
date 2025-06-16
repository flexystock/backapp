<?php

namespace App\Admin\Role\Infrastructure\InputAdapters;

use App\Admin\Role\Application\DTO\AssignRoleRequest;
use App\Admin\Role\Application\InputPorts\AssignRoleUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AssignRoleController extends AbstractController
{
    private AssignRoleUseCaseInterface $useCase;

    public function __construct(AssignRoleUseCaseInterface $useCase)
    {
        $this->useCase = $useCase;
    }

    #[Route('/admin/users/{uuid}/roles', name: 'admin_assign_role', methods: ['POST'])]
    public function __invoke(Request $request, string $uuid): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $role = $data['role'] ?? null;

        if (!$role) {
            return new JsonResponse(['error' => 'ROLE_REQUIRED'], 400);
        }

        $response = $this->useCase->execute(new AssignRoleRequest($uuid, $role));

        if (!$response->isSuccess()) {
            $code = $response->getError() === 'USER_NOT_FOUND' ? 404 : 400;
            return new JsonResponse(['error' => $response->getError()], $code);
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
