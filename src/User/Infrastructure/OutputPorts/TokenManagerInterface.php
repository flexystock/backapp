<?php

namespace App\User\Infrastructure\OutputPorts;

interface TokenManagerInterface
{
    public function create($user): string;
    public function validateToken(string $token): bool;
}