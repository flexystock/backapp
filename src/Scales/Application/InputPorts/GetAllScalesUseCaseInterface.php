<?php

namespace App\Scales\Application\InputPorts;

use App\Scales\Application\DTO\GetAllScalesRequest;
use App\Scales\Application\DTO\GetScaleResponse;

interface GetAllScalesUseCaseInterface
{
    public function execute(GetAllScalesRequest $request): GetScaleResponse;
}
