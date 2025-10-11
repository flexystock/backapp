<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\CreateSubscriptionRequest;
use App\Subscription\Application\DTO\CreateSubscriptionResponse;

interface CreateSubscriptionUseCaseInterface
{
    public function execute(CreateSubscriptionRequest $request): CreateSubscriptionResponse;
}
