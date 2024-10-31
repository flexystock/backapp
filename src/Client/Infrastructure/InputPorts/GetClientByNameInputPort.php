<?php

namespace App\Client\Infrastructure\InputPorts;

use App\Entity\Main\Client;

interface GetClientByNameInputPort
{
    public function getByName(string $name): ?Client;
}