<?php

namespace App\Ttn\Application\OutputPorts;

use App\Ttn\Application\DTO\RegisterAppTtnRequest;
use App\Ttn\Application\DTO\RegisterAppTtnResponse;
use App\Ttn\Application\DTO\RegisterDeviceRequest;
use App\Ttn\Application\DTO\RegisterDeviceResponse;

interface TtnServiceInterface
{
    public function registerDevice(RegisterDeviceRequest $request): RegisterDeviceResponse;

    public function registerApp(RegisterAppTtnRequest $request): RegisterAppTtnResponse;
}
