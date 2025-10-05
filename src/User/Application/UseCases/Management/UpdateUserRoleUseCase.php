<?php

declare(strict_types=1);

namespace App\User\Application\UseCases\Management;

use App\Admin\Role\Application\OutputPorts\Repositories\RoleRepositoryInterface;
use App\Entity\Main\User;
use App\Entity\Main\UserRoleHistory;
use App\User\Application\DTO\Management\UpdateUserRoleRequest;
use App\User\Application\InputPorts\UpdateUserRoleInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class UpdateUserRoleUseCase implements UpdateUserRoleInputPort
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly EntityManagerInterface $entityManager,
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

        $beforeRoles = $this->buildRolesSnapshot($user);

        foreach ($user->getRoleEntities()->toArray() as $existingRole) {
            $user->removeRole($existingRole);
        }

        $user->addRole($role);

        $user->setUuidUserModification($request->getUuidUserModification());
        $user->setDatehourModification(new \DateTimeImmutable());

        $history = new UserRoleHistory();
        $history->setUuidUser((string) $user->getUuid());
        $history->setUuidUserModification($request->getUuidUserModification());
        $history->setDataRolesBeforeModification($this->encodeHistoryPayload($beforeRoles));
        $history->setDataRolesAfterModification($this->encodeHistoryPayload($this->buildRolesSnapshot($user)));
        $history->setDateModification(new \DateTimeImmutable());

        $this->entityManager->persist($history);

        $this->userRepository->save($user);

        return $role->getName();
    }

    private function buildRolesSnapshot(User $user): array
    {
        return [
            'roles' => array_map(static fn($role) => $role->getName(), $user->getRoleEntities()->toArray()),
        ];
    }

    private function encodeHistoryPayload(array $data): string
    {
        $encoded = json_encode($data);
        if (false === $encoded) {
            throw new \RuntimeException('USER_ROLE_HISTORY_SERIALIZATION_FAILED');
        }

        return $encoded;
    }
}
