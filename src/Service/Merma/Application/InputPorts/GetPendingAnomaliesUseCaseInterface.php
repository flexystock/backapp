<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\GetPendingAnomaliesRequest;
use App\Service\Merma\Application\DTO\GetPendingAnomaliesResponse;

interface GetPendingAnomaliesUseCaseInterface
{
    public function execute(GetPendingAnomaliesRequest $request): GetPendingAnomaliesResponse;
}
