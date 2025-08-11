<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\UpdateSubcriptionPlanResponse;
use App\Subscription\Application\DTO\UpdateSubscriptionPlanRequest;

interface UpdateSubscriptionPlanUseCaseInterface
{
    public function execute(UpdateSubscriptionPlanRequest $request): UpdateSubcriptionPlanResponse;
}
