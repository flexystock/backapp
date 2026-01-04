<?php

declare(strict_types=1);

namespace App\Scales\Application\OutputPorts;

use App\Entity\Main\PurchaseScales;

interface PurchaseScalesRepositoryInterface
{
    public function save(PurchaseScales $purchaseScales): void;

    public function findByUuidPurchase(string $uuidPurchase): ?PurchaseScales;

    public function findByUuidClient(string $uuidClient): array;
}
