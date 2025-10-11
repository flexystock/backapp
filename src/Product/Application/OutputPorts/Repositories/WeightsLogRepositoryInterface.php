<?php

// src/Product/Application/OutputPorts/Repositories/WeightsLogRepositoryInterface.php

namespace App\Product\Application\OutputPorts\Repositories;

use App\Entity\Client\WeightsLog;

interface WeightsLogRepositoryInterface
{
    /**
     * Encuentra los weightsLog por UID de producto.
     */
    public function getLatestTotalRealWeightByProduct(int $productId): ?float;

    public function findAllByUuidClient(string $uuidClient): array;

    public function save(WeightsLog $product): void;
}
