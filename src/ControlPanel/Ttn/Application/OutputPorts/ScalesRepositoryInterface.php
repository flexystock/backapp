<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\OutputPorts;

use App\Entity\Client\Scales;

interface ScalesRepositoryInterface
{
    /**
     * Find a scale by its end device ID.
     *
     * @param string $endDeviceId the end device ID
     *
     * @return Scales|null the scale entity or null if not found
     */
    public function findOneByEndDeviceId(string $endDeviceId): ?Scales;

    /**
     * Check if a scale has an associated product.
     *
     * @param string $endDeviceId the end device ID
     *
     * @return bool true if the scale has an associated product, false otherwise
     */
    public function hasAssociatedProduct(string $endDeviceId): bool;
}
