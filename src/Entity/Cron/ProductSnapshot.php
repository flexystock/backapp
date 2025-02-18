<?php
// src/Entity/Cron/ProductSnapshot.php

namespace App\Entity\Cron;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product_snapshot')]
class ProductSnapshot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuidClient;

    #[ORM\Column(type: 'integer')]
    private int $productId;

    #[ORM\Column(type: 'string', length: 50)]
    private string $productName;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private string $percentage; // Usamos string para valores DECIMAL

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    // Getters y Setters

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function setUuidClient(string $uuidClient): self
    {
        $this->uuidClient = $uuidClient;
        return $this;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;
        return $this;
    }

    public function getPercentage(): string
    {
        return $this->percentage;
    }

    public function setPercentage(string $percentage): self
    {
        $this->percentage = $percentage;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
