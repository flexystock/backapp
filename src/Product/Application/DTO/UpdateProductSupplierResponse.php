<?php

namespace App\Product\Application\DTO;

class UpdateProductSupplierResponse
{
    private array $productSupplier;

    public function __construct(array $productSupplier)
    {
        $this->productSupplier = $productSupplier;
    }

    public function getProductSupplier(): array
    {
        return $this->productSupplier;
    }

    public function toArray(): array
    {
        return [
            'productSupplier' => $this->productSupplier
        ];
    }
}
