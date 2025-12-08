<?php

namespace App\IA\Application\DTO;

class CreatePredictionConsumeAllProductResponse
{
    private ?array $predictions;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $predictions, ?string $error, int $statusCode)
    {
        $this->predictions = $predictions;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getPredictions(): ?array
    {
        return $this->predictions;
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
