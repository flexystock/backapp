<?php

namespace App\User\Application\UseCases;

use App\User\Application\InputPorts\GetAllUsersInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

class GetAllUserUseCase implements GetAllUsersInputPort
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAll(): ?array
    {
        return $this->userRepository->findAll();
    }
}
