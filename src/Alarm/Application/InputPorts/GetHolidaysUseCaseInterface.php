<?php

namespace App\Alarm\Application\InputPorts;

use App\Alarm\Application\DTO\GetHolidaysRequest;
use App\Alarm\Application\DTO\GetHolidaysResponse;

interface GetHolidaysUseCaseInterface
{
    public function execute(GetHolidaysRequest $request): GetHolidaysResponse;
}
