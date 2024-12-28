<?php

namespace App\Ttn\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Main\PoolTtnDevice;
use App\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class PoolTtnDeviceRepository implements PoolTtnDeviceRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getAll(): array
    {
        return $this->em->getRepository(PoolTtnDevice::class)->findAll();
    }

    public function createQueryBuilderAllDevices(?bool $available = null): \Doctrine\ORM\QueryBuilder
    {
        $qb = $this->em->createQueryBuilder()
            ->select('d')
            ->from(PoolTtnDevice::class, 'd')
            ->orderBy('d.id', 'DESC');

        if (null !== $available) {
            $qb->andWhere('d.available = :available')
                ->setParameter('available', $available);
        }

        return $qb;
    }
}
