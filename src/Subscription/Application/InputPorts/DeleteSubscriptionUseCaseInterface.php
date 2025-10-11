<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\DeleteSubscriptionRequest;
use App\Subscription\Application\DTO\DeleteSubscriptionResponse;

interface DeleteSubscriptionUseCaseInterface
{
    public function execute(DeleteSubscriptionRequest $request): DeleteSubscriptionResponse;
}
