<?php

namespace App\Report\Application\InputPorts;

use App\Report\Application\DTO\GetAllReportsRequest;
use App\Report\Application\DTO\GetAllReportsResponse;

interface GetAllReportsUseCaseInterface
{
    public function execute(GetAllReportsRequest $request): GetAllReportsResponse;
}
