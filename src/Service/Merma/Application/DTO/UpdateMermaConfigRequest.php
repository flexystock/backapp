<?php

namespace App\Service\Merma\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateMermaConfigRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_PRODUCT_ID')]
    #[Assert\Positive(message: 'INVALID_PRODUCT_ID')]
    private int $productId;

    #[Assert\Range(min: 0, max: 100, notInRangeMessage: 'INVALID_RENDIMIENTO_PCT')]
    private int $rendimientoEsperadoPct;

    private bool $alertOnAnomaly;

    public function __construct(
        string $uuidClient,
        int    $productId,
        int    $rendimientoEsperadoPct,
        bool   $alertOnAnomaly,
    ) {
        $this->uuidClient             = $uuidClient;
        $this->productId              = $productId;
        $this->rendimientoEsperadoPct = $rendimientoEsperadoPct;
        $this->alertOnAnomaly         = $alertOnAnomaly;
    }

    public function getUuidClient(): string         { return $this->uuidClient; }
    public function getProductId(): int             { return $this->productId; }
    public function getRendimientoEsperadoPct(): int { return $this->rendimientoEsperadoPct; }
    public function isAlertOnAnomaly(): bool        { return $this->alertOnAnomaly; }
}
