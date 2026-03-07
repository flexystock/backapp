<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\ScaleEvent;
use App\Service\Merma\Application\OutputPorts\GetPendingAnomaliesRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;

/**
 * Stub — la lógica real usa el EntityManager del cliente directamente.
 */
final class ScaleEventRepository implements ScaleEventRepositoryInterface, GetPendingAnomaliesRepositoryInterface
{
    public function save(ScaleEvent $event): void {}

    public function findById(int $id): ?ScaleEvent
    {
        return null;
    }

    public function sumDeltaByType(
        int $scaleId,
        int $productId,
        string $type,
        \DateTimeInterface $from,
        \DateTimeInterface $to
    ): float {
        return 0.0;
    }

    public function findActiveScaleProductPairsForMonth(\DateTimeInterface $month): array
    {
        return [];
    }

    public function findPendingAnomalies(int $scaleId, int $limit = 10): array
    {
        return [];
    }

    public function countPendingAnomalies(int $scaleId, int $productId): int
    {
        return 0;
    }

    public function findAllPendingAnomalies(): array
    {
        return [];
    }
}