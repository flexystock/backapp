<?php

namespace App\Scales\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\Scales;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ScalesRepository implements ScalesRepositoryInterface
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

    public function save(Scales $scales): void
    {
        $this->em->persist($scales);
        $this->em->flush();
    }

    public function findOneBy(string $endDeviceId): ?Scales
    {
        return $this->em->getRepository(Scales::class)->findOneBy([
            'end_device_id' => $endDeviceId,
        ]);
    }

    public function findOneByProductId(int $productId): ?Scales
    {
        return $this->em->getRepository(Scales::class)->findOneBy([
            'product_id' => $productId,
        ]);
    }

    public function findByUuid(string $uuidScale): ?Scales
    {
        return $this->em->getRepository(Scales::class)->findOneBy([
            'uuid_scale' => $uuidScale,
        ]);
    }

    public function findAllByUuidClient(string $uuidClient): array
    {
        return $this->em->getRepository(Scales::class)->findAll();
    }

    public function remove(Scales $scale): void
    {
        $this->em->remove($scale);
        $this->em->flush();
    }

    public function savePoolScale(\App\Scales\Application\OutputPorts\Scales $scales): void
    {
        // TODO: Implement savePoolScale() method.
    }

    public function findAvailableByEndDeviceId(string $endDeviceId): ?\App\Scales\Application\OutputPorts\Scales
    {
        // TODO: Implement findAvailableByEndDeviceId() method.
    }

    public function findAllIsAvailable(string $available): array
    {
        // TODO: Implement findAllIsAvailable() method.
    }

    public function findUuidByEndDeviceId(string $endDeviceId): ?string
    {
        $scale = $this->em->getRepository(Scales::class)->findOneBy([
            'end_device_id' => $endDeviceId,
        ]);

        return $scale ? $scale->getUuid() : null;
    }

    public function findAllAssignedToProduct(): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('s')
            ->from(\App\Entity\Client\Scales::class, 's')
            ->where('s.product_id IS NOT NULL');

        return $qb->getQuery()->getResult();
    }
}
