<?php

namespace App\Stripe\Application\InputPorts;

use App\Stripe\Application\DTO\PaymentMethodRequest;
use App\Stripe\Application\DTO\PaymentMethodResponse;

interface PaymentMethodUseCaseInterface {
    public function execute(PaymentMethodRequest $request): PaymentMethodResponse;
}