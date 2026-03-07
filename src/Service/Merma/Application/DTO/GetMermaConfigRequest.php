<?php

namespace App\Service\Merma\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetMermaConfigRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_PRODUCT_ID')]
    #[Assert\Positive(message: 'INVALID_PRODUCT_ID')]
    private int $productId;

    public function __construct(string $uuidClient, int $productId)
    {
        $this->uuidClient = $uuidClient;
        $this->productId  = $productId;
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
