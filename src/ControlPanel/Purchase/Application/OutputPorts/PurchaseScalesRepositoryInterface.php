<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\OutputPorts;

use App\Entity\Main\PurchaseScales;

interface PurchaseScalesRepositoryInterface
{
    /**
     * Find all purchase scales requests
     *
     * @return PurchaseScales[]
     */
    public function findAll(): array;

    /**
     * Find purchase scales by UUID purchase
     */
    public function findByUuidPurchase(string $uuidPurchase): ?PurchaseScales;

    /**
     * Find purchase scales by UUID client
     *
     * @return PurchaseScales[]
     */
    public function findByUuidClient(string $uuidClient): array;

    /**
     * Find purchase scales by status
     *
     * @return PurchaseScales[]
     */
    public function findByStatus(string $status): array;
}
