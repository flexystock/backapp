<?php

namespace App\Alarm\Application\InputPorts;

use App\Alarm\Application\DTO\CreateAlarmHolidayRequest;
use App\Alarm\Application\DTO\CreateAlarmHolidayResponse;

interface CreateAlarmHolidayUseCaseInterface
{
    public function execute(CreateAlarmHolidayRequest $request): CreateAlarmHolidayResponse;
}
