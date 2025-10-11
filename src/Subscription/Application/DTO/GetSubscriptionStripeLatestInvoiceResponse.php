<?php

namespace App\Subscription\Application\DTO;

class GetSubscriptionStripeLatestInvoiceResponse
{
    private ?string $clientSecret;
    private ?string $error;

    public function __construct(?string $clientSecret = null, ?string $error = null)
    {
        $this->clientSecret = $clientSecret;
        $this->error = $error;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
