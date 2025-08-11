<?php

namespace App\Ttn\Application\OutputPorts;

use App\Ttn\Application\DTO\RegisterTtnAppRequest;
use App\Ttn\Application\DTO\RegisterTtnAppResponse;
use App\Ttn\Application\DTO\RegisterTtnDeviceRequest;

interface TtnServiceInterface
{
    public function registerDevice(RegisterTtnDeviceRequest $request): void;

    public function registerApp(RegisterTtnAppRequest $request): RegisterTtnAppResponse;

    public function unassignDevice(string $deviceId): void;
}
