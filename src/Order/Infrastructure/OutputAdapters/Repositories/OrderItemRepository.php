<?php

namespace App\Order\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\OrderItem;
use App\Order\Application\OutputPorts\Repositories\OrderItemRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderItemRepository implements OrderItemRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findById(int $id): ?OrderItem
    {
        return $this->em->getRepository(OrderItem::class)->find($id);
    }

    public function findByOrderId(int $orderId): array
    {
        return $this->em->getRepository(OrderItem::class)->findBy(['orderId' => $orderId]);
    }

    public function save(OrderItem $orderItem): void
    {
        $this->em->persist($orderItem);
        $this->em->flush();
    }

    public function remove(OrderItem $orderItem): void
    {
        $this->em->remove($orderItem);
        $this->em->flush();
    }
}
