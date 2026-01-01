<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\OutputPorts;

use App\Entity\Main\PoolTtnDevice;

interface ScaleRepositoryInterface
{
    /**
     * Find all scales (TTN devices) in the pool.
     *
     * @return array array of PoolTtnDevice entities
     */
    public function findAll(): array;

    /**
     * Find a scale by its end device ID.
     *
     * @param string $endDeviceId the scale's end device ID
     *
     * @return PoolTtnDevice|null the PoolTtnDevice entity or null if not found
     */
    public function findOneByEndDeviceId(string $endDeviceId): ?PoolTtnDevice;
}
