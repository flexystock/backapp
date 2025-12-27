<?php

namespace App\Order\Application\OutputPorts\Repositories;

use App\Entity\Client\Order;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function findByStatus(string $status): array;

    public function findBySupplierId(int $clientSupplierId): array;

    public function save(Order $order): void;

    public function remove(Order $order): void;
}
