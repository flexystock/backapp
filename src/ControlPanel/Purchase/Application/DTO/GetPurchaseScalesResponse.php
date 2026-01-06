<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\DTO;

class GetPurchaseScalesResponse
{
    private array $purchases;

    public function __construct(array $purchases)
    {
        $this->purchases = $purchases;
    }

    public function getPurchases(): array
    {
        return $this->purchases;
    }

    public function toArray(): array
    {
        return [
            'purchases' => $this->purchases,
        ];
    }
}
