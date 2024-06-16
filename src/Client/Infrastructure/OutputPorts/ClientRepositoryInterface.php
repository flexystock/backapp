<?php

namespace App\Client\Infrastructure\OutputPorts;

use App\Entity\Main\Client;

interface ClientRepositoryInterface
{
    public function save(Client $client): void;
    public function findByUuid(string $uuid): ?Client;
    // Añade otros métodos que necesites
}