<?php

namespace App\Order\Application\OutputPorts\Repositories;

use App\Entity\Client\OrderHistory;

interface OrderHistoryRepositoryInterface
{
    public function findById(int $id): ?OrderHistory;

    public function findByOrderId(int $orderId): array;

    public function save(OrderHistory $orderHistory): void;
}
