<?php

namespace App\Order\Application\OutputPorts\Repositories;

use App\Entity\Client\OrderItem;

interface OrderItemRepositoryInterface
{
    public function findById(int $id): ?OrderItem;

    public function findByOrderId(int $orderId): array;

    public function save(OrderItem $orderItem): void;

    public function remove(OrderItem $orderItem): void;
}
