<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\DTO;

class GetPurchaseScalesRequest
{
    private ?string $uuidPurchase;
    private ?string $uuidClient;
    private ?string $status;

    public function __construct(?string $uuidPurchase = null, ?string $uuidClient = null, ?string $status = null)
    {
        $this->uuidPurchase = $uuidPurchase;
        $this->uuidClient = $uuidClient;
        $this->status = $status;
    }

    public function getUuidPurchase(): ?string
    {
        return $this->uuidPurchase;
    }

    public function getUuidClient(): ?string
    {
        return $this->uuidClient;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }
}
