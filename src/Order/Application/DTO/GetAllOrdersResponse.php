<?php

namespace App\Order\Application\DTO;

class GetAllOrdersResponse
{
    private ?array $orders;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $orders, ?string $error, int $statusCode)
    {
        $this->orders = $orders;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getOrders(): ?array
    {
        return $this->orders;
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
