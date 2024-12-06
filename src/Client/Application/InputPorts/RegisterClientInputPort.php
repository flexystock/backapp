<?php

namespace App\Client\Application\InputPorts;

use App\Client\Application\DTO\RegisterClientRequest;
use App\Entity\Main\Client;

interface RegisterClientInputPort
{
    public function register(RegisterClientRequest $request): Client;
}
