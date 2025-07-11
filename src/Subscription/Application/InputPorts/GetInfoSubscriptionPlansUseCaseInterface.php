<?php

namespace App\Subscription\Application\InputPorts;
use App\Subscription\Application\DTO\GetInfoSubscriptionPlansResponse;

interface GetInfoSubscriptionPlansUseCaseInterface
{
    public function execute(): GetInfoSubscriptionPlansResponse;
}