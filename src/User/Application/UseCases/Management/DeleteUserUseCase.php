<?php

declare(strict_types=1);

namespace App\User\Application\UseCases\Management;

use App\User\Application\DTO\Management\DeleteUserRequest;
use App\User\Application\InputPorts\DeleteUserInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

class DeleteUserUseCase implements DeleteUserInputPort
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function delete(DeleteUserRequest $request): void
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

        foreach ($user->getRoleEntities()->toArray() as $role) {
            $user->removeRole($role);
        }

        foreach ($user->getClients()->toArray() as $client) {
            $user->removeClient($client);
        }

        $this->userRepository->delete($user);
    }
}
