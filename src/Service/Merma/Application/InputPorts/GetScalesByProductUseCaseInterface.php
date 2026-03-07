<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\GetScalesByProductRequest;
use App\Service\Merma\Application\DTO\GetScalesByProductResponse;

interface GetScalesByProductUseCaseInterface
{
    public function execute(GetScalesByProductRequest $request): GetScalesByProductResponse;
}
