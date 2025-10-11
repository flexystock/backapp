<?php

namespace App\Stripe\Application\DTO;

class PaymentMethodResponse
{
    public function __construct(public ?string $paymentMethodId)
    {
    }
}
