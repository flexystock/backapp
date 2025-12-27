<?php

namespace App\Supplier\Application\OutputPorts\Repositories;

use App\Entity\Client\ClientSupplier;

interface ClientSupplierRepositoryInterface
{
    public function findAll(): array;
    
    public function findById(int $id): ?ClientSupplier;
    
    public function findBySupplierId(int $supplierId): ?ClientSupplier;
    
    public function save(ClientSupplier $clientSupplier): void;
    
    public function delete(ClientSupplier $clientSupplier): void;
}
