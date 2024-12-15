<?php

// src/Product/Infrastructure/OutputAdapters/Repositories/ProductRepository.php

namespace App\Product\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\Product;
use App\Product\Application\OutputPorts\Repositories\ProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProductRepository implements ProductRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Encuentra un producto por UUID y UUID del cliente.
     */
    public function findByUuidAndClient(string $uuidProduct, string $uuidClient): ?Product
    {
        // Si cada cliente tiene su propia base de datos, solo necesitas buscar por UUID

        return $this->em->getRepository(Product::class)->findOneBy([
            'uuid' => $uuidProduct,
        ]);

        // Si compartes una base de datos con segregación por uuid_client, usa:
        /*
        return $this->em->getRepository(Product::class)->findOneBy([
            'uuid' => $uuidProduct,
            'client' => $uuidClient,
        ]);
        */
    }

    /**
     * Encuentra todos los productos por  UUID del cliente.
     */
    public function findAllByUuidClient(string $uuidClient): array
    {
        // Si cada cliente tiene su propia base de datos, solo necesitas buscar por UUID

        return $this->em->getRepository(Product::class)->findAll();
    }

    public function save(Product $product): void
    {
        $this->em->persist($product);
        $this->em->flush();
    }

    public function remove(Product $product)
    {
        $this->em->remove($product);
        $this->em->flush();
    }
}
