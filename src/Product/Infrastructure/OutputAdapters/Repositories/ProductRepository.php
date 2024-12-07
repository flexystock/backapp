<?php
namespace App\Product\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\Product;
use App\Product\Application\OutputPorts\Repositories\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private EntityManagerInterface $clientEm;
    private \Doctrine\ORM\EntityRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(ClientEntityManagerProvider $provider)
    {
        $this->em = $provider->getEntityManager();
        $this->repository = $this->em->getRepository(Product::class);
    }

    public function findProductByUuid(string $uuid): ?Product
    {
        return $this->clientEm->getRepository(Product::class)->find($uuid);
    }
}
