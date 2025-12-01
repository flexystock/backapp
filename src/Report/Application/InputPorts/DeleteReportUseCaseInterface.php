<?php

namespace App\Report\Application\InputPorts;

use App\Report\Application\DTO\DeleteReportRequest;
use App\Report\Application\DTO\DeleteReportResponse;

interface DeleteReportUseCaseInterface
{
    public function execute(DeleteReportRequest $request): DeleteReportResponse;
}
