<?php

namespace App\Scales\Application\InputPorts;

use App\Scales\Application\DTO\GetScaleRequest;
use App\Scales\Application\DTO\GetScaleResponse;

interface GetScaleUseCaseInterface
{
    public function execute(GetScaleRequest $request): GetScaleResponse;
}
