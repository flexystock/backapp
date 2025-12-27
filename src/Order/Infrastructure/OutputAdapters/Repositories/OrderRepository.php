<?php

namespace App\Order\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\Order;
use App\Order\Application\OutputPorts\Repositories\OrderRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class OrderRepository implements OrderRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findById(int $id): ?Order
    {
        return $this->em->getRepository(Order::class)->find($id);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return $this->em->getRepository(Order::class)->findOneBy(['orderNumber' => $orderNumber]);
    }

    public function findByStatus(string $status): array
    {
        return $this->em->getRepository(Order::class)->findBy(['status' => $status]);
    }

    public function findBySupplierId(int $clientSupplierId): array
    {
        return $this->em->getRepository(Order::class)->findBy(['clientSupplierId' => $clientSupplierId]);
    }

    public function save(Order $order): void
    {
        $order->setUpdatedAt(new \DateTime());
        $this->em->persist($order);
        $this->em->flush();
    }

    public function remove(Order $order): void
    {
        $this->em->remove($order);
        $this->em->flush();
    }
}
