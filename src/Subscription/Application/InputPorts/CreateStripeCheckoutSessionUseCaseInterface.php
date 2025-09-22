<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\CreateStripeCheckoutSessionRequest;
use App\Subscription\Application\DTO\CreateStripeCheckoutSessionResponse;

interface CreateStripeCheckoutSessionUseCaseInterface
{
    public function execute(CreateStripeCheckoutSessionRequest $request): CreateStripeCheckoutSessionResponse;
}
