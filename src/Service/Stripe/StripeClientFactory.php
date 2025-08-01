<?php

namespace App\Service\Stripe;

use Stripe\StripeClient;

class StripeClientFactory
{
    public function __construct(
        private string $stripeSecretKey
    ) {

    }

    public function create(): StripeClient
    {
        return new StripeClient($this->stripeSecretKey);
    }

    public function get(): StripeClient
    {
        static $client = null;

        if ($client === null) {
            $client = $this->create();
        }

        return $client;
    }
}