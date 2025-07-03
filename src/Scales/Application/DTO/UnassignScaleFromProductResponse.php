<?php

namespace App\Scales\Application\DTO;

class UnassignScaleFromProductResponse
{
    private ?array $scale;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $scale, ?string $error, int $statusCode)
    {
        $this->scale = $scale;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getScale(): ?array
    {
        return $this->scale;
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