<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\CheckSubscriptionStatusRequest;
use App\Subscription\Application\DTO\CheckSubscriptionStatusResponse;

interface CheckSubscriptionStatusUseCaseInterface
{
    public function execute(CheckSubscriptionStatusRequest $request): CheckSubscriptionStatusResponse;
}
