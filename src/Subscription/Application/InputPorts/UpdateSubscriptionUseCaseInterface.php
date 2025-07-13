<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\UpdateSubscriptionRequest;
use App\Subscription\Application\DTO\UpdateSubscriptionResponse;

interface UpdateSubscriptionUseCaseInterface
{
    public function execute(UpdateSubscriptionRequest $request): UpdateSubscriptionResponse;
}
