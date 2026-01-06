<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\OutputPorts;

use App\Entity\Client\PoolScale;

interface PoolScalesRepositoryInterface
{
    /**
     * Find a scale in the pool by its end device ID.
     *
     * @param string $endDeviceId the end device ID
     *
     * @return PoolScale|null the scale entity or null if not found
     */
    public function findOneByEndDeviceId(string $endDeviceId): ?PoolScale;

    /**
     * Delete a scale from the pool.
     *
     * @param PoolScale $scale the scale to delete
     */
    public function delete(PoolScale $scale): void;

    /**
     * Save a scale in the pool.
     *
     * @param PoolScale $scale the scale to save
     */
    public function save(PoolScale $scale): void;
}
