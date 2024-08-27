<?php

namespace App\User\Infrastructure\InputPorts;
use App\Entity\Main\User;
interface GetAllUsersInputPort
{
    public function getAll(): ?array;
}