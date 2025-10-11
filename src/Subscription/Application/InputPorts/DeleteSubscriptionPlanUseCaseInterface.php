<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\DeleteSubscriptionPlanRequest;
use App\Subscription\Application\DTO\DeleteSubscriptionPlanResponse;

interface DeleteSubscriptionPlanUseCaseInterface
{
    public function execute(DeleteSubscriptionPlanRequest $request): DeleteSubscriptionPlanResponse;
}
