<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\UpdateSubscriptionPlanRequest;
use App\Subscription\Application\DTO\UpdateSubcriptionPlanResponse;

interface UpdateSubscriptionPlanUseCaseInterface
{
    public function execute(UpdateSubscriptionPlanRequest $request): UpdateSubcriptionPlanResponse;
}
