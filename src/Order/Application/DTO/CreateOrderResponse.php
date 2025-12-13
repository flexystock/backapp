<?php

namespace App\Order\Application\DTO;

class CreateOrderResponse
{
    private ?array $order;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $order, ?string $error, int $statusCode)
    {
        $this->order = $order;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getOrder(): ?array
    {
        return $this->order;
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
