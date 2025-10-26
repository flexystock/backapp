<?php

namespace App\Alarm\Application\InputPorts;

use App\Alarm\Application\DTO\GetAlarmBatteryShelveRequest;
use App\Alarm\Application\DTO\GetAlarmBatteryShelveResponse;

interface GetAlarmBatteryShelveUseCaseInterface
{
    public function execute(GetAlarmBatteryShelveRequest $request): GetAlarmBatteryShelveResponse;
}
