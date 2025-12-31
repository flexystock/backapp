<?php

namespace App\Supplier\Application\DTO;

class UpdateSupplierResponse
{
    private ?array $supplier;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $supplier, ?string $error = null, int $statusCode = 200)
    {
        $this->supplier = $supplier;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getSupplier(): ?array
    {
        return $this->supplier;
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
