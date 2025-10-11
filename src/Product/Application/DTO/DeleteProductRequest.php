<?php

namespace App\Product\Application\DTO;

class DeleteProductRequest
{
    #[Assert\Uuid(message: 'REQUIRED_CLIENT_ID')]
    private string $uuidClient;
    #[Assert\Uuid(message: 'REQUIRED_PRODUCT_ID')]
    private string $uuidProduct;

    public function __construct(string $uuidClient, string $uuidProduct)
    {
        $this->uuidClient = $uuidClient;
        $this->uuidProduct = $uuidProduct;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUuidProduct(): string
    {
        return $this->uuidProduct;
    }
}
