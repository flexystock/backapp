<?php

namespace App\User\Infrastructure\InputPorts;

use App\Entity\Main\User;
use App\User\Application\DTO\CreateUserRequest;

interface RegisterUserInputPort
{
    public function register(CreateUserRequest $user): User;
}