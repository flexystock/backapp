<?php

namespace App\Client\Infrastructure\OutputPorts;

use App\Entity\Main\Client;

interface ClientRepositoryInterface
{
    public function save(Client $client): void;
    public function findByUuid(string $uuid): ?Client;
    public function findByName(string $name): ?Client;
}