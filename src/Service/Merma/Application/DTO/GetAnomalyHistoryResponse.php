<?php

namespace App\Service\Merma\Application\DTO;

class GetAnomalyHistoryResponse
{
    public function __construct(
        public readonly array $anomalies,
    ) {}
}
