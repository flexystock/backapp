<?php

namespace App\Service\Merma\Application\OutputPorts;

use App\Entity\Client\Product;

interface MermaProductRepositoryInterface
{
    public function findById(int $productId): ?Product;
}