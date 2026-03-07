<?php

namespace App\Service\Merma\Application\DTO;

class GetPendingAnomaliesResponse
{
    /**
     * @param array<array{
     *     id: int,
     *     scale_id: int,
     *     product_id: int,
     *     weight_before: float,
     *     weight_after: float,
     *     delta_kg: float,
     *     delta_cost: float|null,
     *     detected_at: string,
     *     notes: string|null
     * }> $anomalies
     */
    public function __construct(
        public readonly array $anomalies,
    ) {}
}
