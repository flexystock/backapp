<?php

namespace App\Subscription\Application\DTO;

class GetInfoSubscriptionPlansResponse
{
    private ?array $plan;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $plan, ?string $error, int $statusCode)
    {
        $this->plan = $plan;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getPlan(): ?array
    {
        return $this->plan;
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
