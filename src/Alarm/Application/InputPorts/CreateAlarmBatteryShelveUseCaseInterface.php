<?php

namespace App\Alarm\Application\InputPorts;

use App\Alarm\Application\DTO\CreateAlarmBatteryShelveRequest;
use App\Alarm\Application\DTO\CreateAlarmBatteryShelveResponse;

interface CreateAlarmBatteryShelveUseCaseInterface
{
    public function execute(CreateAlarmBatteryShelveRequest $request): CreateAlarmBatteryShelveResponse;
}
