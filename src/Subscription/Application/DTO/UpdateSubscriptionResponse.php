<?php

namespace App\Subscription\Application\DTO;

class UpdateSubscriptionResponse
{
    private ?array $subscription;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $subscription, ?string $error, int $statusCode)
    {
        $this->subscription = $subscription;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getSubscription(): ?array
    {
        return $this->subscription;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
