<?php

namespace App\Client\Application\InputPorts;

interface GetAllClientsInputPort
{
    public function getAll(): ?array;
}
