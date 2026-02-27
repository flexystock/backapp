<?php

namespace App\Ttn\Application\OutputPorts;

use App\Entity\Main\PoolTtnDevice;

interface PoolTtnDeviceRepositoryInterface
{
    public function getAll(): array;

    public function findOneBy(string $endDeviceId): ?PoolTtnDevice;

    public function findLastDevice(): ?PoolTtnDevice;
}
