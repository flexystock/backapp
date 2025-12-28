<?php

namespace App\Product\Application\OutputPorts\Repositories;

use App\Entity\Client\ProductSupplier;

interface ProductSupplierRepositoryInterface
{
    public function findById(int $id): ?ProductSupplier;

    public function findByProductId(int $productId): array;

    public function findByProductIdAndClientSupplierId(int $productId, int $clientSupplierId): ?ProductSupplier;

    public function findPreferredByProductId(int $productId): ?ProductSupplier;

    public function save(ProductSupplier $productSupplier): void;

    public function remove(ProductSupplier $productSupplier): void;
}
