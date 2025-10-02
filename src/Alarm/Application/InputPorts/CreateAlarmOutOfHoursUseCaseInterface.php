<?php

namespace App\Alarm\Application\InputPorts;

use App\Alarm\Application\DTO\CreateAlarmOutOfHoursRequest;
use App\Alarm\Application\DTO\CreateAlarmOutOfHoursResponse;

interface CreateAlarmOutOfHoursUseCaseInterface
{
    public function execute(CreateAlarmOutOfHoursRequest $request): CreateAlarmOutOfHoursResponse;
}
