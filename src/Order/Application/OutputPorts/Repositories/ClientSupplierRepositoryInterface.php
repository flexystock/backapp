<?php

namespace App\Order\Application\OutputPorts\Repositories;

use App\Entity\Client\ClientSupplier;

interface ClientSupplierRepositoryInterface
{
    public function findById(int $id): ?ClientSupplier;

    public function findBySupplierId(int $supplierId): ?ClientSupplier;

    public function findAll(): array;

    public function findActive(): array;

    public function save(ClientSupplier $clientSupplier): void;

    public function remove(ClientSupplier $clientSupplier): void;
}
