<?php
// src/User/Application/LoginUseCase.php
namespace App\User\Application;

use App\User\Domain\Entity\User;
use App\User\Infrastructure\InputPorts\LoginInputPort;
use App\User\Infrastructure\OutputPorts\TokenManagerInterface;

class LoginUseCase implements LoginInputPort
{
    private $tokenManager;

    public function __construct(TokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    public function generateToken(string $userId, string $clientUuid): string
    {
        $userData = [
            'user_id' => $userId,
            'client_uuid' => $clientUuid
        ];

        return $this->tokenManager->generateToken($userData);
    }

    public function login(string $email, string $password): ?User
    {
        // TODO: Implement login() method.
    }
}
