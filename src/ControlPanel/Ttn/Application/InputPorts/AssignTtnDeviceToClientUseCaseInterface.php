<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\InputPorts;

use App\ControlPanel\Ttn\Application\DTO\AssignTtnDeviceToClientRequest;
use App\ControlPanel\Ttn\Application\DTO\AssignTtnDeviceToClientResponse;

interface AssignTtnDeviceToClientUseCaseInterface
{
    /**
     * Assigns a TTN device to a specific client.
     * Updates end_device_name in pool_ttn_device and creates record in client's pool_scales.
     *
     * @param AssignTtnDeviceToClientRequest $request the request containing device ID and client UUID
     *
     * @return AssignTtnDeviceToClientResponse the response indicating success or failure
     */
    public function execute(AssignTtnDeviceToClientRequest $request): AssignTtnDeviceToClientResponse;
}
