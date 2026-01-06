<?php

declare(strict_types=1);

namespace App\Scales\Application\DTO;

class PurchaseScalesRequest
{
    private string $uuidClient;
    private int $numScales;

    public function __construct(string $uuidClient, int $numScales)
    {
        $this->uuidClient = $uuidClient;
        $this->numScales = $numScales;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getNumScales(): int
    {
        return $this->numScales;
    }
}
