<?php

namespace App\Scales\Application\InputPorts;

use App\Scales\Application\DTO\UpdateScaleRequest;
use App\Scales\Application\DTO\UpdateScaleResponse;

interface UpdateScaleUseCaseInterface
{
    public function execute(UpdateScaleRequest $request): UpdateScaleResponse;
}
