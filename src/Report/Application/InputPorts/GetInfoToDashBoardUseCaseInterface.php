<?php

namespace App\Report\Application\InputPorts;

use App\Report\Application\DTO\GetInfoToDashBoardRequest;
use App\Report\Application\DTO\GetInfoToDashBoardResponse;

interface GetInfoToDashBoardUseCaseInterface
{
    public function execute(GetInfoToDashBoardRequest $request): GetInfoToDashBoardResponse;
}
