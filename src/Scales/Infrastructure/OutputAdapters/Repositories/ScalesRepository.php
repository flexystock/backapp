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
}
