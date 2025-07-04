<?php

namespace App\WeightAnalytics\Application\OutputPorts\Repositories;

interface WeightsLogRepositoryInterface
{
    public function getProductWeightSummary(string $productId, ?\DateTimeInterface $from, ?\DateTimeInterface $to): array;
}