<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\GetInfoSubscriptionRequest;
use App\Subscription\Application\DTO\GetInfoSubscriptionResponse;

interface GetInfoSubscriptionUseCaseInterface
{
    public function execute(GetInfoSubscriptionRequest $request): GetInfoSubscriptionResponse;
}
