<?php
namespace App\Product\Application\OutputPorts\Repositories;

use App\Entity\Client\Product;

interface ProductRepositoryInterface
{
    public function findByUuid(string $uuid): ?Product;
}
