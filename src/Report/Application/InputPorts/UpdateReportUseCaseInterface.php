<?php

namespace App\Report\Application\InputPorts;

use App\Report\Application\DTO\UpdateReportRequest;
use App\Report\Application\DTO\UpdateReportResponse;

interface UpdateReportUseCaseInterface
{
    public function execute(UpdateReportRequest $request): UpdateReportResponse;
}
