<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\WeightsLog;
use App\Service\Merma\Application\OutputPorts\ScaleReadingRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class WeightsLogReadingAdapter implements ScaleReadingRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function findWeightAt(int $scaleId, \DateTimeInterface $at): ?float
    {
        $result = $this->entityManager
            ->getRepository(WeightsLog::class)
            ->createQueryBuilder('w')
            ->where('w.scale = :scaleId')
            ->andWhere('w.date <= :at')
            ->setParameter('scaleId', $scaleId)
            ->setParameter('at', $at)
            ->orderBy('w.date', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result?->getRealWeight();
    }

    public function findLastWeightBefore(int $scaleId, \DateTimeInterface $before): ?float
    {
        return $this->findWeightAt($scaleId, $before);
    }
}
