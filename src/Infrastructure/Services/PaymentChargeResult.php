<?php

namespace App\Infrastructure\Services;

use App\Entity\Main\PaymentTransaction;

class PaymentChargeResult
{
    private PaymentTransaction $transaction;
    private ?string $clientSecret;

    public function __construct(PaymentTransaction $transaction, ?string $clientSecret)
    {
        $this->transaction = $transaction;
        $this->clientSecret = $clientSecret;
    }

    public function getTransaction(): PaymentTransaction
    {
        return $this->transaction;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }
}
