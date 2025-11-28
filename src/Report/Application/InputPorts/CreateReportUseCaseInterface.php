<?php

namespace App\Report\Application\InputPorts;

use App\Report\Application\DTO\CreateReportRequest;
use App\Report\Application\DTO\CreateReportResponse;

interface CreateReportUseCaseInterface
{
    public function execute(CreateReportRequest $request): CreateReportResponse;
}
