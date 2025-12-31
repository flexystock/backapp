<?php

namespace App\Supplier\Application\DTO;

class UpdateSupplierClientResponse
{
    private ?array $clientSupplier;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $clientSupplier, ?string $error, int $statusCode)
    {
        $this->clientSupplier = $clientSupplier;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getClientSupplier(): ?array
    {
        return $this->clientSupplier;
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
