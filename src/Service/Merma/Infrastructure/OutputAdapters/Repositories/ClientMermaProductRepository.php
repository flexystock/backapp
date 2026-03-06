<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\Product;
use App\Service\Merma\Application\OutputPorts\MermaProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class ClientMermaProductRepository implements MermaProductRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function findById(int $productId): ?Product
    {
        return $this->em->getRepository(Product::class)->find($productId);
    }
}