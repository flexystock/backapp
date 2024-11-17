<?php

namespace App\User\Infrastructure\OutputAdapters;

use App\User\Application\OutputPorts\TokenManagerInterface;

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
