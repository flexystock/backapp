<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Main\SubscriptionPlan;
use App\Subscription\Application\OutputPorts\SubscriptionPlanRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionPlanRepository extends ServiceEntityRepository implements SubscriptionPlanRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, SubscriptionPlan::class);
    }

    public function save(SubscriptionPlan $subscriptionPlan): void
    {
        $this->entityManager->persist($subscriptionPlan);
        $this->entityManager->flush();
    }

    public function findByUuid(string $id): ?SubscriptionPlan
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function findByName(string $name): ?SubscriptionPlan
    {
        return $this->findOneBy(['name' => $name]);
    }
    /**
     * @return SubscriptionPlan[]
     */
    public function findAll(): array
    {
        return parent::findAll();
    }

}
