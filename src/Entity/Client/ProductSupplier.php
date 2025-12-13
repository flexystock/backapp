<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'product_suppliers')]
#[ORM\UniqueConstraint(name: 'unique_product_supplier', columns: ['product_id', 'client_supplier_id'])]
#[ORM\Index(name: 'idx_is_preferred', columns: ['is_preferred'])]
#[ORM\Index(name: 'idx_product_id', columns: ['product_id'])]
#[ORM\Index(name: 'idx_client_supplier_id', columns: ['client_supplier_id'])]
class ProductSupplier
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(name: 'product_id', type: 'integer')]
    private int $productId;

    #[ORM\Column(name: 'client_supplier_id', type: 'integer')]
    private int $clientSupplierId;

    #[ORM\Column(name: 'is_preferred', type: 'boolean')]
    private bool $isPreferred = false;

    #[ORM\Column(name: 'product_code', type: 'string', length: 100, nullable: true)]
    private ?string $productCode = null;

    #[ORM\Column(name: 'unit_price', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $unitPrice = null;

    #[ORM\Column(name: 'min_order_quantity', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $minOrderQuantity = null;

    #[ORM\Column(name: 'delivery_days', type: 'integer', nullable: true)]
    private ?int $deliveryDays = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getClientSupplierId(): int
    {
        return $this->clientSupplierId;
    }

    public function setClientSupplierId(int $clientSupplierId): self
    {
        $this->clientSupplierId = $clientSupplierId;
        return $this;
    }

    public function isPreferred(): bool
    {
        return $this->isPreferred;
    }

    public function setIsPreferred(bool $isPreferred): self
    {
        $this->isPreferred = $isPreferred;
        return $this;
    }

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function setProductCode(?string $productCode): self
    {
        $this->productCode = $productCode;
        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(?float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function getMinOrderQuantity(): ?float
    {
        return $this->minOrderQuantity;
    }

    public function setMinOrderQuantity(?float $minOrderQuantity): self
    {
        $this->minOrderQuantity = $minOrderQuantity;
        return $this;
    }

    public function getDeliveryDays(): ?int
    {
        return $this->deliveryDays;
    }

    public function setDeliveryDays(?int $deliveryDays): self
    {
        $this->deliveryDays = $deliveryDays;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
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
