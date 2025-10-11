<?php

namespace App\Stripe\Application\InputPorts;

use App\Stripe\Application\DTO\SetupIntentRequest;
use App\Stripe\Application\DTO\SetupIntentResponse;

interface SetupIntentUseCaseInterface
{
    public function execute(SetupIntentRequest $request): SetupIntentResponse;
}
