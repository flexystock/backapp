<?php

namespace App\User\Application\InputPorts\Auth;

use App\Entity\Main\User;
use App\User\Application\DTO\Auth\CreateUserRequest;

interface RegisterUserInputPort
{
    public function register(CreateUserRequest $user): User;
}
