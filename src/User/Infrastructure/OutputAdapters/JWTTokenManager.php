<?php

// src/User/Infrastructure/OutputAdapters/JWTTokenManager.php
namespace App\User\Infrastructure\OutputAdapters;

use App\User\Infrastructure\OutputPorts\TokenManagerInterface;
use Firebase\JWT\JWT;

class JWTTokenManager implements TokenManagerInterface
{
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function generateToken(array $userData): string
    {
        $payload = [
            'iss' => 'your-issuer',
            'aud' => 'your-audience',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'data' => $userData
        ];

        return JWT::encode($payload, $this->secretKey);
    }

    public function create($user): string
    {
        // TODO: Implement create() method.
    }

    public function validateToken(string $token): bool
    {
        // TODO: Implement validateToken() method.
    }
}
