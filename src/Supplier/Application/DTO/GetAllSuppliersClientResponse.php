<?php

namespace App\Supplier\Application\DTO;

class GetAllSuppliersClientResponse
{
    private ?array $suppliers;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $suppliers, ?string $error, int $statusCode)
    {
        $this->suppliers = $suppliers;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getSuppliers(): ?array
    {
        return $this->suppliers;
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
