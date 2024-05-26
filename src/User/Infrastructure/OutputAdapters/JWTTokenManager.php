<?php

namespace App\User\Infrastructure\OutputAdapters;

use App\User\Infrastructure\OutputPorts\TokenManagerInterface;
use Firebase\JWT\JWT;

class JWTTokenManager implements TokenManagerInterface {
    // implementación de los métodos...
    public function create($user): string
    {
        // TODO: Implement create() method.
    }

    public function validateToken(string $token): bool
    {
        // TODO: Implement validateToken() method.
    }
}
