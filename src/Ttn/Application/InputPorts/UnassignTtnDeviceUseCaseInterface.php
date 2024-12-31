<?php

namespace App\Ttn\Application\InputPorts;

use App\Ttn\Application\DTO\UnassignTtnDeviceRequest;
use App\Ttn\Application\DTO\UnassignTtnDeviceResponse;
interface UnassignTtnDeviceUseCaseInterface
{
    public function execute(UnassignTtnDeviceRequest $request): UnassignTtnDeviceResponse;

}