<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\GetMermaMonthlyHistoryRequest;
use App\Service\Merma\Application\DTO\MermaReportDTO;

interface GetMermaMonthlyHistoryUseCaseInterface
{
    /**
     * @return MermaReportDTO[]
     */
    public function execute(GetMermaMonthlyHistoryRequest $request): array;
}
