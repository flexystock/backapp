<?php

namespace App\Stripe\Application\DTO;

class SetupIntentRequest
{
    public function __construct(public string $uuidClient)
    {
    }
}
