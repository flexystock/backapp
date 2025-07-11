<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\CreateSubscriptionPlanRequest;
use App\Entity\Main\SubscriptionPlan;

interface CreateSubscriptionPlanUseCaseInterface
{
    public function execute(CreateSubscriptionPlanRequest $subscriptionPlanRequest): SubscriptionPlan;
}
