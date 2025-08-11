<?php

namespace App\WeightAnalytics\Application\InputPorts;

use App\WeightAnalytics\Application\DTO\GetProductWeightSummaryRequest;
use App\WeightAnalytics\Application\DTO\GetProductWeightSummaryResponse;

interface GetProductWeightSummaryUseCaseInterface
{
    public function execute(GetProductWeightSummaryRequest $request): GetProductWeightSummaryResponse;
}
