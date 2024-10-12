<?php

namespace App\Client\Infrastructure\InputPorts;

use App\Client\Application\DTO\CreateClientRequest;
use App\Entity\Main\Client;

interface CreateClientInputPort
{
    public function create(CreateClientRequest $request): Client;
}