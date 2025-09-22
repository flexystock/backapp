<?php

namespace App\Subscription\Application\OutputPorts;

interface StripeServiceInterface
{
    public function createCheckoutSession(
        string $priceId,
        string $userEmail,
        string $clientUuid,
        string $planId,
        string $successUrl,
        string $cancelUrl
    ): array;
}
