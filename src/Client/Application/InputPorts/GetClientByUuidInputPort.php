<?php

namespace App\Client\Application\InputPorts;

use App\Entity\Main\Client;

interface GetClientByUuidInputPort
{
    public function getByUuid(string $uuid): ?Client;
}
