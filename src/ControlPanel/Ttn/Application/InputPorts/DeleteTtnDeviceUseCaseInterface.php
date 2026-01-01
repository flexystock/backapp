<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\InputPorts;

use App\ControlPanel\Ttn\Application\DTO\DeleteTtnDeviceRequest;
use App\ControlPanel\Ttn\Application\DTO\DeleteTtnDeviceResponse;

interface DeleteTtnDeviceUseCaseInterface
{
    /**
     * Deletes a TTN device from TTN network and associated database tables.
     *
     * @param DeleteTtnDeviceRequest $request the request containing the end device ID to delete
     *
     * @return DeleteTtnDeviceResponse the response indicating success or failure
     */
    public function execute(DeleteTtnDeviceRequest $request): DeleteTtnDeviceResponse;
}
