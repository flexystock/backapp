<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\GetMonthlyTimelineRequest;
use App\Service\Merma\Application\DTO\GetMonthlyTimelineResponse;

interface GetMonthlyTimelineUseCaseInterface
{
    public function execute(GetMonthlyTimelineRequest $request): GetMonthlyTimelineResponse;
}
