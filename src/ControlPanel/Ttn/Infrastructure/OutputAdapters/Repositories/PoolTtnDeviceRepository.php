<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Infrastructure\OutputAdapters\Repositories;

use App\ControlPanel\Ttn\Application\OutputPorts\PoolTtnDeviceRepositoryInterface;
use App\Entity\Main\PoolTtnDevice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class PoolTtnDeviceRepository extends ServiceEntityRepository implements PoolTtnDeviceRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, PoolTtnDevice::class);
        $this->entityManager = $entityManager;
    }

    public function findOneByEndDeviceId(string $endDeviceId): ?PoolTtnDevice
    {
        return $this->findOneBy(['end_device_id' => $endDeviceId]);
    }

    public function delete(PoolTtnDevice $device): void
    {
        $this->entityManager->remove($device);
    }
}
