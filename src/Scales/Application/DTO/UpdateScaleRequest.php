<?php

namespace App\Scales\Application\DTO;

class UpdateScaleRequest
{
    private string $uuidClient;
    private string $uuidScale;
    private ?int $productId = null;
    private ?int $posX = null;
    private ?int $width = null;
    private ?string $uuidUserModification = null;
    private ?\DateTimeInterface $datehourModification = null;

    public function __construct(string $uuidClient, string $uuidScale)
    {
        $this->uuidClient = $uuidClient;
        $this->uuidScale = $uuidScale;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUuidScale(): string
    {
        return $this->uuidScale;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): void
    {
        $this->productId = $productId;
    }

    public function getPosX(): ?int
    {
        return $this->posX;
    }

    public function setPosX(?int $posX): void
    {
        $this->posX = $posX;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): void
    {
        $this->width = $width;
    }

    public function getUuidUserModification(): ?string
    {
        return $this->uuidUserModification;
    }

    public function setUuidUserModification(?string $uuidUserModification): void
    {
        $this->uuidUserModification = $uuidUserModification;
    }

    public function getDatehourModification(): ?\DateTimeInterface
    {
        return $this->datehourModification;
    }

    public function setDatehourModification(?\DateTimeInterface $datehourModification): void
    {
        $this->datehourModification = $datehourModification;
    }
}
