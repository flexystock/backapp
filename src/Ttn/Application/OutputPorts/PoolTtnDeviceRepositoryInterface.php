<?php

namespace App\Ttn\Application\OutputPorts;

use App\Entity\Main\PoolTtnDevice;

interface PoolTtnDeviceRepositoryInterface
{
    public function getAll(): array;

    public function findLastDevice(): ?PoolTtnDevice;
}
