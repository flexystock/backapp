<?php

namespace App\Scales\Application\InputPorts;
use App\Scales\Application\DTO\UnassignScaleFromProductResponse;
use App\Scales\Application\DTO\UnassignScaleFromProductRequest;

interface UnassignScaleFromProductUseCaseInterface
{
    public function execute(UnassignScaleFromProductRequest $request): UnassignScaleFromProductResponse;

}