<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\GetAnomalyHistoryRequest;
use App\Service\Merma\Application\DTO\GetAnomalyHistoryResponse;

interface GetAnomalyHistoryUseCaseInterface
{
    public function execute(GetAnomalyHistoryRequest $request): GetAnomalyHistoryResponse;
}
