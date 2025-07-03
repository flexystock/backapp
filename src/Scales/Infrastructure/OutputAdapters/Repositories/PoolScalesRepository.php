<?php

namespace App\Scales\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\PoolScale;
use App\Scales\Application\OutputPorts\PoolScalesRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
class PoolScalesRepository implements PoolScalesRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function selectClientConnection(string $uuidClient): void
    {
        // Ejemplo: un "ConnectionManager" que setea el $this->em
        // a la BBDD que corresponda.
        // O un "em->changeDatabase($uuidClient)" ...
        // Esto depende mucho de tu proyecto.
    }

    public function savePoolScale(PoolScale $scales): void
    {
        $this->em->persist($scales);
        $this->em->flush();
    }
    public function findOneBy(string $endDeviceId): ?PoolScale
    {
        return $this->em->getRepository(PoolScale::class)->findOneBy([
            'end_device_id' => $endDeviceId,
        ]);
    }

    public function findAvailableByEndDeviceId(string $endDeviceId): ?PoolScale
    {
        return $this->em->getRepository(PoolScale::class)->findOneBy([
            'end_device_id' => $endDeviceId,
            'available' => true,
        ]);
    }
    public function findAllIsAvailable(string $available): array
    {
        return $this->em->getRepository(PoolScale::class)->findBy([
            'available' => $available,
        ]);
    }

    public function remove(PoolScale $scale): void
    {
        $this->em->remove($scale);
        $this->em->flush();
    }

    public function findOneByUuidScale(string $uuidScale): ?PoolScale
    {
        return $this->em->getRepository(PoolScale::class)->findOneBy([
            'uuid_scale' => $uuidScale,
        ]);
    }
}
