<?php

namespace App\User\Application\InputPorts;

interface GetUsersByClientInputPort
{
    /**
     * @param string $clientUuid
     * @return array
     */
    public function getUsersByClient(string $clientUuid): array;
}
