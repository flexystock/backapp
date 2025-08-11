<?php

namespace App\WeightAnalytics\Infrastructure\OutputAdapters\Repositories;

use App\WeightAnalytics\Application\OutputPorts\Repositories\WeightsLogRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class WeightsLogRepository implements WeightsLogRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getProductWeightSummary(string $productId, ?\DateTimeInterface $from, ?\DateTimeInterface $to): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('w')
            ->from(\App\Entity\Client\WeightsLog::class, 'w')
            ->where('w.product = :productId')
            ->setParameter('productId', $productId); // Esto funciona si $productId es el id, Doctrine lo resuelve

        if ($from) {
            $qb->andWhere('w.date >= :from')
                ->setParameter('from', $from);
        }
        if ($to) {
            $qb->andWhere('w.date <= :to')
                ->setParameter('to', $to);
        }

        $qb->orderBy('w.date', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
