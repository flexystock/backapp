<?php

namespace App\Client\Infrastructure\InputPorts;

use App\Entity\Main\Client;

interface CreateClientInputPort
{
    public function create(array $data): Client;
}