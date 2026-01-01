<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Infrastructure\OutputAdapters\Repositories;

use App\ControlPanel\Ttn\Application\OutputPorts\PoolScalesRepositoryInterface;
use App\Entity\Client\PoolScale;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class PoolScalesRepository extends ServiceEntityRepository implements PoolScalesRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, PoolScale::class);
        $this->entityManager = $entityManager;
    }

    public function findOneByEndDeviceId(string $endDeviceId): ?PoolScale
    {
        return $this->findOneBy(['end_device_id' => $endDeviceId]);
    }

    public function delete(PoolScale $scale): void
    {
        $this->entityManager->remove($scale);
    }
}
