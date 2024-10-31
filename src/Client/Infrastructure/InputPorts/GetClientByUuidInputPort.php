<?php

namespace App\Client\Infrastructure\InputPorts;

use App\Entity\Main\Client;

interface GetClientByUuidInputPort
{
    public function getByUuid(string $uuid): ?Client;
}