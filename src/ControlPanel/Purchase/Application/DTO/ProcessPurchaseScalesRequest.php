<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\DTO;

class ProcessPurchaseScalesRequest
{
    private string $uuidPurchase;
    private string $uuidUser;

    public function __construct(string $uuidPurchase, string $uuidUser)
    {
        $this->uuidPurchase = $uuidPurchase;
        $this->uuidUser = $uuidUser;
    }

    public function getUuidPurchase(): string
    {
        return $this->uuidPurchase;
    }

    public function getUuidUser(): string
    {
        return $this->uuidUser;
    }
}
