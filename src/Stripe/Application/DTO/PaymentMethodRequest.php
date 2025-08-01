<?php

namespace App\Stripe\Application\DTO;

class PaymentMethodRequest
{
    private string $uuidClient;
    public function __construct(string $uuidClient)
    {
        $this->uuidClient = $uuidClient;
    }
    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }
    public function setUuidClient(string $uuidClient): void
    {
        $this->uuidClient = $uuidClient;
    }
}
