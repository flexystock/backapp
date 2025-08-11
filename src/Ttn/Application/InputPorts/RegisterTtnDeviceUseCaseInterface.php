<?php

namespace App\Ttn\Application\InputPorts;

use App\Ttn\Application\DTO\RegisterTtnDeviceRequest;
use App\Ttn\Application\DTO\RegisterTtnDeviceResponse;

interface RegisterTtnDeviceUseCaseInterface
{
    public function execute(RegisterTtnDeviceRequest $request): RegisterTtnDeviceResponse;
}
