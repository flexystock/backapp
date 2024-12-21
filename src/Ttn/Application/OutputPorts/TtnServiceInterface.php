<?php
// src/Ttn/Application/OutputPorts/TtnServiceInterface.php
namespace App\Ttn\Application\OutputPorts;

use App\Ttn\Application\DTO\RegisterDeviceRequest;
use App\Ttn\Application\DTO\RegisterDeviceResponse;

interface TtnServiceInterface
{
    public function registerDevice(RegisterDeviceRequest $request): RegisterDeviceResponse;
}