<?php

namespace App\User\Infrastructure\InputPorts;

use App\User\Domain\Entity\User;

interface LoginInputPort
{
    public function login(string $email, string $password): ?User;
}