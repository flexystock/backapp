<?php

namespace App\Order\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\OrderHistory;
use App\Order\Application\OutputPorts\Repositories\OrderHistoryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderHistoryRepository implements OrderHistoryRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findById(int $id): ?OrderHistory
    {
        return $this->em->getRepository(OrderHistory::class)->find($id);
    }

    public function findByOrderId(int $orderId): array
    {
        return $this->em->getRepository(OrderHistory::class)->findBy(
            ['orderId' => $orderId],
            ['createdAt' => 'ASC']
        );
    }

    public function save(OrderHistory $orderHistory): void
    {
        $this->em->persist($orderHistory);
        $this->em->flush();
    }
}
