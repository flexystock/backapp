<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\CreateSubscriptionPlanRequest;
use App\Subscription\Application\DTO\CreateSubscriptionPlanResponse;

interface CreateSubscriptionPlanUseCaseInterface
{
    public function execute(CreateSubscriptionPlanRequest $subscriptionPlanRequest): CreateSubscriptionPlanResponse;
}
