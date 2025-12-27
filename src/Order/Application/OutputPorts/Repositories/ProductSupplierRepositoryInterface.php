<?php

namespace App\Order\Application\OutputPorts\Repositories;

use App\Entity\Client\ProductSupplier;

interface ProductSupplierRepositoryInterface
{
    public function findById(int $id): ?ProductSupplier;

    public function findByProductId(int $productId): array;

    public function findPreferredByProductId(int $productId): ?ProductSupplier;

    public function save(ProductSupplier $productSupplier): void;

    public function remove(ProductSupplier $productSupplier): void;
}
