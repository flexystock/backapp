<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\InputPorts;

use App\ControlPanel\Purchase\Application\DTO\ProcessPurchaseScalesRequest;
use App\ControlPanel\Purchase\Application\DTO\ProcessPurchaseScalesResponse;

interface ProcessPurchaseScalesUseCaseInterface
{
    public function execute(ProcessPurchaseScalesRequest $request): ProcessPurchaseScalesResponse;
}
