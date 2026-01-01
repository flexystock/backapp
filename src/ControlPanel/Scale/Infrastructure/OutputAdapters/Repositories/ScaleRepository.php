<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Infrastructure\OutputAdapters\Repositories;

use App\ControlPanel\Scale\Application\OutputPorts\ScaleRepositoryInterface;
use App\Entity\Main\PoolTtnDevice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ScaleRepository extends ServiceEntityRepository implements ScaleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PoolTtnDevice::class);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function findOneByEndDeviceId(string $endDeviceId): ?PoolTtnDevice
    {
        return $this->findOneBy(['end_device_id' => $endDeviceId]);
    }
}
