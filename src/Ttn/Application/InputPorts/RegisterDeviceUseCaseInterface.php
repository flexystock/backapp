<?php

namespace App\Ttn\Application\InputPorts;

use App\Ttn\Application\DTO\RegisterDeviceRequest;
use App\Ttn\Application\DTO\RegisterDeviceResponse;

interface RegisterDeviceUseCaseInterface
{
    public function execute(RegisterDeviceRequest $request): RegisterDeviceResponse;
}