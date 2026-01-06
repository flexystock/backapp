<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\OutputPorts;

use App\Entity\Main\Client;

interface ClientRepositoryInterface
{
    public function findByUuid(string $uuidClient): ?Client;
}
