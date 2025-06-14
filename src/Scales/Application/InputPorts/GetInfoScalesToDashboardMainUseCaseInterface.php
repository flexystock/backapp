<?php

namespace App\Scales\Application\InputPorts;

use App\Scales\Application\DTO\GetInfoScalesToDashboardMainRequest;
use App\Scales\Application\DTO\GetInfoScalesToDashboardMainResponse;

interface GetInfoScalesToDashboardMainUseCaseInterface
{
    public function execute(GetInfoScalesToDashboardMainRequest $request): GetInfoScalesToDashboardMainResponse;
}
