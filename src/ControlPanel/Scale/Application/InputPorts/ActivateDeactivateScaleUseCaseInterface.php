<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\InputPorts;

use App\ControlPanel\Scale\Application\DTO\ActivateDeactivateScaleRequest;
use App\ControlPanel\Scale\Application\DTO\ActivateDeactivateScaleResponse;

interface ActivateDeactivateScaleUseCaseInterface
{
    public function execute(ActivateDeactivateScaleRequest $request): ActivateDeactivateScaleResponse;
}
