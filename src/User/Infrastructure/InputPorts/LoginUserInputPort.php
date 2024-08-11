<?php

namespace App\User\Infrastructure\InputPorts;

use App\Entity\Main\User;

interface LoginUserInputPort
{
    public function login(string $mail, string $password, string $ipAddress): ?User;
}