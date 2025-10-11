<?php

namespace App\Subscription\Application\DTO;

class GetSubscriptionStripeLatestInvoiceRequest
{
    private string $subscriptionUuid;

    public function __construct(string $subscriptionUuid)
    {
        $this->subscriptionUuid = $subscriptionUuid;
    }

    public function getSubscriptionUuid(): string
    {
        return $this->subscriptionUuid;
    }
}
