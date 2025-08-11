<?php

namespace App\Scales\Application\InputPorts;

use App\Scales\Application\DTO\UnassignScaleFromProductRequest;
use App\Scales\Application\DTO\UnassignScaleFromProductResponse;

interface UnassignScaleFromProductUseCaseInterface
{
    public function execute(UnassignScaleFromProductRequest $request): UnassignScaleFromProductResponse;
}
