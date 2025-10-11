<?php

namespace App\WeightAnalytics\Application\DTO;

class GetProductWeightSummaryRequest
{
    private string $uuidClient;
    private string $productId;
    private string $from;
    private string $to;

    public function __construct(
        string $uuidClient,
        string $productId,
        string $from,
        string $to
    ) {
        $this->uuidClient = $uuidClient;
        $this->productId = $productId;
        $this->from = $from;
        $this->to = $to;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getTo(): string
    {
        return $this->to;
    }
}
