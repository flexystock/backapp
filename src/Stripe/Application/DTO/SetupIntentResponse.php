<?php

namespace App\Stripe\Application\DTO;

class SetupIntentResponse
{
    public function __construct(public string $clientSecret)
    {
    }
}
