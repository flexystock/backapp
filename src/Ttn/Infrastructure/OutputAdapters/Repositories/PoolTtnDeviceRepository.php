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

    public function createQueryBuilderAllDevices(?bool $available = null, ?string $uuidClient = null): \Doctrine\ORM\QueryBuilder
    {
        $qb = $this->em->createQueryBuilder()
            ->select('d')
            ->from(PoolTtnDevice::class, 'd')
            ->orderBy('d.id', 'DESC');

        if (null !== $available) {
            $qb->andWhere('d.available = :available')
                ->setParameter('available', $available);
        }

        if (null !== $uuidClient) {
            $qb->andWhere('d.end_device_name = :end_device_name')
                ->setParameter('end_device_name', $uuidClient);
        }

        return $qb;
    }

    public function findOneBy(string $endDeviceId): ?PoolTtnDevice
    {
        return $this->em->getRepository(PoolTtnDevice::class)->findOneBy([
            'end_device_id' => $endDeviceId,
        ]);
    }

    public function update(PoolTtnDevice $device): void
    {
        $this->em->persist($device);
        $this->em->flush();
    }

    public function save(PoolTtnDevice $device): void
    {
        $this->em->persist($device);
        $this->em->flush();
    }
}
