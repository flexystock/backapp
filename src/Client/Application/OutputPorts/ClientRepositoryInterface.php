<?php

namespace App\Client\Application\OutputPorts;

use App\Entity\Main\Client;

interface ClientRepositoryInterface
{
    public function save(Client $client): void;
    public function findByUuid(string $uuid): ?Client;
    public function findByName(string $name): ?Client;
}