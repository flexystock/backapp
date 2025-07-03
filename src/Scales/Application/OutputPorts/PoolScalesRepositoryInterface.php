<?php

namespace App\Scales\Application\OutputPorts;

use App\Entity\Client\PoolScale;

interface PoolScalesRepositoryInterface
{
    public function selectClientConnection(string $uuidClient): void;
    public function savePoolScale(PoolScale $poolScale): void;
    public function findOneBy(string $endDeviceId): ?PoolScale;
    public function findAvailableByEndDeviceId(string $endDeviceId): ?PoolScale;
    public function findAllIsAvailable(string $available): array;
    public function remove(PoolScale $poolScale): void;

    public function findOneByUuidScale(string $uuidScale): ?PoolScale;
}
