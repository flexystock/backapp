<?php

namespace App\User\Application\InputPorts;

use App\Client\Application\DTO\ClientDTOCollection;
interface GetUserClientsInterface
{
    public function getUserClients(string $userId): ClientDTOCollection;
}