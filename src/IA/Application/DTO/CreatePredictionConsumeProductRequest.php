<?php

namespace App\IA\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePredictionConsumeProductRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_UUID_CLIENT')]
    #[Assert\Uuid(message: 'INVALID_UUID_CLIENT')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_PRODUCT_ID')]
    #[Assert\Type(type: 'integer', message: 'INVALID_PRODUCT_ID')]
    private int $productId;

    public function __construct(string $uuidClient, int $productId)
    {
        $this->uuidClient = $uuidClient;
        $this->productId = $productId;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
}
