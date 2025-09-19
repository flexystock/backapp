<?php

declare(strict_types=1);

namespace App\Subscription\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Main\Client;
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

    public function findByUuidClient(string $uuid): ?Subscription
    {
        return $this->findOneBy(['client_uuid' => $uuid]);
    }

    public function findOneByUuid(string $uuid): ?Subscription
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

    public function hasActiveSubscriptionForClient(Client $client): bool
    {
        $qb = $this->createQueryBuilder('s')
            ->select('1')
            ->andWhere('s.client = :client')
            ->andWhere('s.isActive = :isActive')
            ->andWhere('s.paymentStatus = :paymentStatus')
            ->andWhere('(s.endedAt IS NULL OR s.endedAt > :now)')
            ->setParameter('client', $client)
            ->setParameter('isActive', true)
            ->setParameter('paymentStatus', 'paid')
            ->setParameter('now', new \DateTime())
            ->setMaxResults(1);

        return null !== $qb->getQuery()->getOneOrNullResult();
    }

    public function findByStripeSubscriptionId(string $stripeSubscriptionId): ?Subscription
    {
        return $this->findOneBy(['stripeSubscriptionId' => $stripeSubscriptionId]);
    }

    /**
     * @return Subscription[]
     */
    public function findActiveByClient(Client $client): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.client = :client')
            ->andWhere('s.isActive = :isActive')
            ->andWhere('s.paymentStatus = :paymentStatus')
            ->andWhere('(s.endedAt IS NULL OR s.endedAt > :now)')
            ->setParameter('client', $client)
            ->setParameter('isActive', true)
            ->setParameter('paymentStatus', 'paid')
            ->setParameter('now', new \DateTime())
            ->orderBy('s.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
