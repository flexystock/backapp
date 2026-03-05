<?php

namespace App\Service\Merma\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetMermaMonthlyHistoryRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_SCALE_ID')]
    #[Assert\Positive(message: 'INVALID_SCALE_ID')]
    private int $scaleId;

    #[Assert\NotBlank(message: 'REQUIRED_PRODUCT_ID')]
    #[Assert\Positive(message: 'INVALID_PRODUCT_ID')]
    private int $productId;

    #[Assert\Positive(message: 'INVALID_LIMIT')]
    private int $limit;

    public function __construct(string $uuidClient, int $scaleId, int $productId, int $limit = 12)
    {
        $this->uuidClient = $uuidClient;
        $this->scaleId    = $scaleId;
        $this->productId  = $productId;
        $this->limit      = $limit;
    }

    public function getUuidClient(): string { return $this->uuidClient; }
    public function getScaleId(): int       { return $this->scaleId; }
    public function getProductId(): int     { return $this->productId; }
    public function getLimit(): int         { return $this->limit; }
}
