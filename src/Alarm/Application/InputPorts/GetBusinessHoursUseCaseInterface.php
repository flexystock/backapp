<?php

namespace App\Alarm\Application\InputPorts;

use App\Alarm\Application\DTO\GetBusinessHoursRequest;
use App\Alarm\Application\DTO\GetBusinessHoursResponse;

interface GetBusinessHoursUseCaseInterface
{
    public function execute(GetBusinessHoursRequest $request): GetBusinessHoursResponse;
}
