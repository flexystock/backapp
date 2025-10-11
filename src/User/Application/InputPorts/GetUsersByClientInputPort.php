<?php

namespace App\User\Application\InputPorts;

interface GetUsersByClientInputPort
{
    public function getUsersByClient(string $clientUuid): array;
}
