<?php

namespace App\Scales\Application\InputPorts;

use App\Scales\Application\DTO\AssignScaleToProductRequest;
use App\Scales\Application\DTO\AssignScaleToProductResponse;
interface AssignScaleToProductUseCaseInterface
{
    public function execute(AssignScaleToProductRequest $request): AssignScaleToProductResponse;

}