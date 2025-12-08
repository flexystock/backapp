<?php

namespace App\IA\Application\DTO;

class CreatePredictionConsumeProductResponse
{
    private ?array $prediction;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $prediction, ?string $error, int $statusCode)
    {
        $this->prediction = $prediction;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getPrediction(): ?array
    {
        return $this->prediction;
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
