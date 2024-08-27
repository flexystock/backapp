<?php

namespace App\User\Application;

use App\Entity\Main\User;
use App\User\Infrastructure\InputPorts\GetAllUsersInputPort;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
class GetAllUserUseCase implements GetAllUsersInputPort
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository = $userRepository;
    }

    public function getAll(): ?array
    {
        //$users = $this->userRepository->findAll();
        //var_dump($users);
        //die("llegamos al useCase");
        return $this->userRepository->findAll();
    }
}