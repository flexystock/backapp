<?php

declare(strict_types=1);

namespace App\User\Application\UseCases\Management;

use App\User\Application\DTO\Management\ToggleUserActiveRequest;
use App\User\Application\InputPorts\ToggleUserActiveInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

class ToggleUserActiveUseCase implements ToggleUserActiveInputPort
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function toggle(ToggleUserActiveRequest $request): bool
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

        $newStatus = !$user->isActive();
        $user->setActive($newStatus);

        $this->userRepository->save($user);

        return $newStatus;
    }
}
