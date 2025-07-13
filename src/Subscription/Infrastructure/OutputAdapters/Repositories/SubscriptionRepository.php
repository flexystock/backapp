<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Main\Subscription;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionRepository extends ServiceEntityRepository implements SubscriptionRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, Subscription::class);
    }

    public function save(Subscription $subscription): void
    {
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();
    }

    public function remove(Subscription $subscription): void
    {
        $this->entityManager->remove($subscription);
        $this->entityManager->flush();
    }

    public function findByUuid(string $uuid): ?Subscription
    {
        return $this->findOneBy(['uuidSubscription' => $uuid]);
    }

    /**
     * @return Subscription[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }
}
