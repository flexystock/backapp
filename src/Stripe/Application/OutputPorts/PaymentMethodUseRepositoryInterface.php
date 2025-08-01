<?php

namespace App\Stripe\Application\OutputPorts;

interface PaymentMethodUseRepositoryInterface
{
    public function getDefaultPaymentMethod(string $uuidClient): ?string;
}
