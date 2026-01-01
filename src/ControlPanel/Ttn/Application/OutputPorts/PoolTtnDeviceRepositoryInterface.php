<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\OutputPorts;

use App\Entity\Main\PoolTtnDevice;

interface PoolTtnDeviceRepositoryInterface
{
    /**
     * Find a device in the pool by its end device ID.
     *
     * @param string $endDeviceId the end device ID
     *
     * @return PoolTtnDevice|null the device entity or null if not found
     */
    public function findOneByEndDeviceId(string $endDeviceId): ?PoolTtnDevice;

    /**
     * Delete a device from the pool.
     *
     * @param PoolTtnDevice $device the device to delete
     */
    public function delete(PoolTtnDevice $device): void;
}
