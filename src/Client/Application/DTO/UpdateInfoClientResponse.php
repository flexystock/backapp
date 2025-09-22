<?php

namespace App\Client\Application\DTO;

class UpdateInfoClientResponse
{
    private ?array $client;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $client, ?string $error, int $statusCode)
    {
        $this->client = $client;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getClient(): ?array
    {
        return $this->client;
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
