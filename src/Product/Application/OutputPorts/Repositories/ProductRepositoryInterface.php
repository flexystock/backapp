<?php

// src/Product/Application/OutputPorts/Repositories/ProductRepositoryInterface.php

namespace App\Product\Application\OutputPorts\Repositories;

use App\Entity\Client\Product;

interface ProductRepositoryInterface
{
    /**
     * Encuentra un producto por UUID y UUID del cliente.
     */
    public function findByUuidAndClient(string $uuidProduct, string $uuidClient): ?Product;

    public function findAllByUuidClient(string $uuidClient): array;

    public function save(Product $product): void;
}
