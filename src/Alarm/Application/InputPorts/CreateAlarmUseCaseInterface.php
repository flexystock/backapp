<?php
// src/Alarm/Application/InputPorts/CreateAlarmUseCaseInterface.php
namespace App\Alarm\Application\InputPorts;

use App\Alarm\Application\DTO\CreateAlarmRequest;
use App\Alarm\Application\DTO\CreateAlarmResponse;

interface CreateAlarmUseCaseInterface
{
    public function execute(CreateAlarmRequest $request): CreateAlarmResponse;
}
