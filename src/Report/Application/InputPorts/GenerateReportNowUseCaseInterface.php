<?php

namespace App\Report\Application\InputPorts;

use App\Report\Application\DTO\GenerateReportNowRequest;
use App\Report\Application\DTO\GenerateReportNowResponse;

interface GenerateReportNowUseCaseInterface
{
    public function execute(GenerateReportNowRequest $request): GenerateReportNowResponse;
}
