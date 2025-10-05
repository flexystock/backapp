<?php

declare(strict_types=1);

namespace App\User\Application\UseCases\Management;

use App\Admin\Role\Application\OutputPorts\Repositories\RoleRepositoryInterface;
use App\User\Application\DTO\Management\UpdateUserRoleRequest;
use App\User\Application\InputPorts\UpdateUserRoleInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

class UpdateUserRoleUseCase implements UpdateUserRoleInputPort
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
    ) {
    }

    public function update(UpdateUserRoleRequest $request): string
    {
        $user = $this->userRepository->findByEmail($request->getUserEmail());
        if (null === $user) {
            throw new \RuntimeException('USER_NOT_FOUND');
        }

        $isAssociatedWithClient = false;
        foreach ($user->getClients() as $client) {
            if ($client->getUuidClient() === $request->getUuidClient()) {
                $isAssociatedWithClient = true;
                break;
            }
        }

        if (false === $isAssociatedWithClient) {
            throw new \RuntimeException('USER_NOT_ASSOCIATED_WITH_CLIENT');
        }

        $role = $this->roleRepository->findByName($request->getUserRol());
        if (null === $role) {
            throw new \RuntimeException('ROLE_NOT_FOUND');
        }

        foreach ($user->getRoleEntities()->toArray() as $existingRole) {
            $user->removeRole($existingRole);
        }

        $user->addRole($role);
        $this->userRepository->save($user);

        return $role->getName();
    }
}
