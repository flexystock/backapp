<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\OutputPorts;

use App\Entity\Main\Client;

interface ClientRepositoryInterface
{
    /**
     * Find a client by their UUID.
     *
     * @param string $uuid the client's UUID
     *
     * @return Client|null the Client entity or null if not found
     */
    public function findOneByUuid(string $uuid): ?Client;

    /**
     * Find multiple clients by their UUIDs.
     *
     * @param array $uuids array of client UUIDs
     *
     * @return array array of Client entities indexed by UUID
     */
    public function findByUuids(array $uuids): array;
}
