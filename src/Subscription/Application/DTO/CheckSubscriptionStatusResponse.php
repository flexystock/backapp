<?php

namespace App\Subscription\Application\DTO;

class CheckSubscriptionStatusResponse
{
    private ?array $data;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $data, ?string $error = null, int $statusCode = 200)
    {
        $this->data = $data;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getData(): ?array
    {
        return $this->data;
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