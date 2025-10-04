<?php

namespace App\Dashboard\Application\InputPorts;

use App\Dashboard\Application\DTO\GetDashboardSummaryRequest;
use App\Dashboard\Application\DTO\GetDashboardSummaryResponse;

interface GetDashboardSummaryUseCaseInterface
{
    public function execute(GetDashboardSummaryRequest $request): GetDashboardSummaryResponse;
}
