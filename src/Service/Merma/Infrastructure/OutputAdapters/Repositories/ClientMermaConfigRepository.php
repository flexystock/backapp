<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\MermaConfig;
use App\Entity\Client\Product;
use App\Service\Merma\Application\OutputPorts\MermaConfigRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Implementación real del repositorio de configuración de merma.
 * Se instancia con el EntityManager del cliente (multi-tenant).
 */
final class ClientMermaConfigRepository implements MermaConfigRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function save(MermaConfig $config): void
    {
        $this->em->persist($config);
        $this->em->flush();
    }

    public function findByProductId(int $productId): ?MermaConfig
    {
        return $this->em->createQuery(
            'SELECT c FROM App\Entity\Client\MermaConfig c WHERE IDENTITY(c.product) = :productId'
        )
            ->setParameter('productId', $productId)
            ->getOneOrNullResult();
    }

    public function createDefaultForProduct(int $productId): MermaConfig
    {
        $product = $this->em->getRepository(Product::class)->find($productId);
        if ($product === null) {
            throw new \RuntimeException("PRODUCT_NOT_FOUND:{$productId}");
        }

        $config = new MermaConfig();
        $config->setProduct($product);

        $this->em->persist($config);
        $this->em->flush();

        return $config;
    }
}
