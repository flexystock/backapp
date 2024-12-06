<?php

namespace App\User\Application\InputPorts;

interface GetAllUsersInputPort
{
    public function getAll(): ?array;
}
