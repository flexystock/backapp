<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\OutputPorts;

interface TtnApiServiceInterface
{
    /**
     * Delete a device from The Things Network.
     *
     * @param string $endDeviceId the end device ID to delete
     *
     * @return bool true if deletion was successful, false otherwise
     */
    public function deleteDevice(string $endDeviceId): bool;
}
