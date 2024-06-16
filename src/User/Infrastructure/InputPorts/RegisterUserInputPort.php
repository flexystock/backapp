<?php

namespace App\User\Infrastructure\InputPorts;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Main\User;

interface RegisterUserInputPort
{
    public function register(array $data): User;
}