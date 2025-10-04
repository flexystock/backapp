<?php

declare(strict_types=1);

namespace App\User\Application\UseCases\Management;

use App\User\Application\DTO\Management\VerifyUserRequest;
use App\User\Application\InputPorts\VerifyUserInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

class VerifyUserUseCase implements VerifyUserInputPort
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function verify(VerifyUserRequest $request): void
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

        if ($user->isVerified()) {
            throw new \RuntimeException('USER_ALREADY_VERIFIED');
        }

        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $user->setVerificationTokenExpiresAt(null);

        $this->userRepository->save($user);
    }
}
