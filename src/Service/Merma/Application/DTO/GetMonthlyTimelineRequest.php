<?php

namespace App\Service\Merma\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetMonthlyTimelineRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\Positive(message: 'INVALID_SCALE_ID')]
    private int $scaleId;

    #[Assert\Positive(message: 'INVALID_PRODUCT_ID')]
    private int $productId;

    public function __construct(string $uuidClient, int $scaleId, int $productId)
    {
        $this->uuidClient = $uuidClient;
        $this->scaleId    = $scaleId;
        $this->productId  = $productId;
    }

    public function getUuidClient(): string { return $this->uuidClient; }

    public function getScaleId(): int { return $this->scaleId; }

    public function getProductId(): int { return $this->productId; }
}
