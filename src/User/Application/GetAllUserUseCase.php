<?php

namespace App\User\Application;


use App\User\Infrastructure\InputPorts\GetAllUsersInputPort;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
class GetAllUserUseCase implements GetAllUsersInputPort
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    public function getAll(): ?array
    {
        return $this->userRepository->findAll();
    }
}