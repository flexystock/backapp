<?php

namespace App\Scales\Application\InputPorts;

use App\Scales\Application\DTO\RegisterScalesRequest;
use App\Scales\Application\DTO\RegisterScalesResponse;

interface RegisterScalesUseCaseInterface
{
    public function execute(RegisterScalesRequest $request): RegisterScalesResponse;
}
