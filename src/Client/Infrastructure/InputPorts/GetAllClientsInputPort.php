<?php

namespace App\Client\Infrastructure\InputPorts;

interface GetAllClientsInputPort
{
    public function getAll(): ?array;
}