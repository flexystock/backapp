<?php

namespace App\Report\Application\InputPorts;

use App\Report\Application\DTO\GetReportRequest;
use App\Report\Application\DTO\GetReportResponse;

interface GetReportUseCaseInterface
{
    public function execute(GetReportRequest $request): GetReportResponse;
}
