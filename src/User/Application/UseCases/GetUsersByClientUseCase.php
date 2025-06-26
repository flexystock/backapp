<?php

namespace App\User\Application\UseCases;

use App\User\Application\InputPorts\GetUsersByClientInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

class GetUsersByClientUseCase implements GetUsersByClientInputPort
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUsersByClient(string $clientUuid): array
    {
        return $this->userRepository->findByClientUuid($clientUuid);
    }
}
