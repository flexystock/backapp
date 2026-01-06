<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Infrastructure\OutputAdapters\Repositories;

use App\ControlPanel\Ttn\Application\OutputPorts\ScalesRepositoryInterface;
use App\Entity\Client\Scales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ScalesRepository extends ServiceEntityRepository implements ScalesRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Scales::class);
    }

    public function findOneByEndDeviceId(string $endDeviceId): ?Scales
    {
        return $this->findOneBy(['end_device_id' => $endDeviceId]);
    }

    public function hasAssociatedProduct(string $endDeviceId): bool
    {
        $scale = $this->findOneByEndDeviceId($endDeviceId);

        return $scale && $scale->getProduct() !== null;
    }
}
