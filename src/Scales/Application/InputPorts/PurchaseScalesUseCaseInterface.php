<?php

declare(strict_types=1);

namespace App\Scales\Application\InputPorts;

use App\Scales\Application\DTO\PurchaseScalesRequest;
use App\Scales\Application\DTO\PurchaseScalesResponse;

interface PurchaseScalesUseCaseInterface
{
    public function execute(PurchaseScalesRequest $request): PurchaseScalesResponse;
}
