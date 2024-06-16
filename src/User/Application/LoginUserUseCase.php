<?php

namespace App\User\Application;

use App\Entity\Main\User;
use App\User\Infrastructure\InputPorts\LoginUserInputPort;

use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class LoginUserUseCase implements LoginUserInputPort
{
    private UserRepositoryInterface $userRepository;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserRepositoryInterface $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    public function login(string $mail, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($mail);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return null;
        }
        return $user;
    }

}