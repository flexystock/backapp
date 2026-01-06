<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\InputPorts;

use App\ControlPanel\Purchase\Application\DTO\GetPurchaseScalesRequest;
use App\ControlPanel\Purchase\Application\DTO\GetPurchaseScalesResponse;

interface GetPurchaseScalesUseCaseInterface
{
    public function execute(GetPurchaseScalesRequest $request): GetPurchaseScalesResponse;
}
