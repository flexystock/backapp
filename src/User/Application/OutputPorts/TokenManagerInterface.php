<?php

namespace App\User\Application\OutputPorts;

interface TokenManagerInterface
{
    public function create($user): string;
    public function validateToken(string $token): bool;
}