<?php

namespace App\Admin\Role\Application\UseCases;

use App\Admin\Role\Application\DTO\AssignRoleRequest;
use App\Admin\Role\Application\DTO\AssignRoleResponse;
use App\Admin\Role\Application\InputPorts\AssignRoleUseCaseInterface;
use App\Admin\Role\Application\OutputPorts\Repositories\RoleRepositoryInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

class AssignRoleUseCase implements AssignRoleUseCaseInterface
{
    private UserRepositoryInterface $userRepository;
    private RoleRepositoryInterface $roleRepository;

    public function __construct(UserRepositoryInterface $userRepository, RoleRepositoryInterface $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function execute(AssignRoleRequest $request): AssignRoleResponse
    {
        $user = $this->userRepository->findByUuid($request->getUserUuid());
        if (!$user) {
            return new AssignRoleResponse(false, 'USER_NOT_FOUND');
        }

        $role = $this->roleRepository->findByName($request->getRoleName());
        if (!$role) {
            return new AssignRoleResponse(false, 'ROLE_NOT_FOUND');
        }

        $user->addRole($role);

        $this->userRepository->save($user);

        return new AssignRoleResponse(true);
    }
}
