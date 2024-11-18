<?php

namespace App\Client\Application\InputPorts;

use App\Entity\Main\Client;

interface GetClientByNameInputPort
{
    public function getByName(string $name): ?Client;
}