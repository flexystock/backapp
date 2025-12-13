<?php

namespace App\Supplier\Application\OutputPorts\Repositories;

use App\Entity\Main\Supplier;

interface SupplierRepositoryInterface
{
    public function findById(int $id): ?Supplier;

    public function findBySlug(string $slug): ?Supplier;

    public function findAll(): array;

    public function findActive(): array;

    public function save(Supplier $supplier): void;

    public function remove(Supplier $supplier): void;
}
