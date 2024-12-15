<?php

namespace App\Product\Application\DTO;

class UpdateProductResponse
{
    private ?array $product;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $product = null, ?string $error = null, int $statusCode = 200)
    {
        $this->product = $product;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getProduct(): ?array
    {
        return $this->product;
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
