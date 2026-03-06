<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\MermaConfig;
use App\Service\Merma\Application\OutputPorts\MermaConfigRepositoryInterface;

/**
 * Stub — la lógica real se ejecuta en HandleTtnUplinkUseCase
 * usando el EntityManager del cliente directamente.
 */
final class MermaConfigRepository implements MermaConfigRepositoryInterface
{
    public function save(MermaConfig $config): void {}

    public function findByProductId(int $productId): ?MermaConfig
    {
        return null;
    }

    public function createDefaultForProduct(int $productId): MermaConfig
    {
        $config = new MermaConfig();
        
        return $config;
    }
}
