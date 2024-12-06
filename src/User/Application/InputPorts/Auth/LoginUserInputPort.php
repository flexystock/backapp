<?php

namespace App\User\Application\InputPorts\Auth;

use App\Entity\Main\User;

interface LoginUserInputPort
{
    public function login(string $mail, string $password, string $ipAddress): ?User;

    public function handleFailedLogin(User $user): ?string;
}
