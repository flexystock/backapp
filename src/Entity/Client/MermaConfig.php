<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'merma_config')]
#[ORM\HasLifecycleCallbacks]
class MermaConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\Column(name: 'rendimiento_esperado_pct', type: 'smallint', options: ['unsigned' => true])]
    private int $rendimientoEsperadoPct = 80;

    #[ORM\Column(name: 'alert_on_anomaly', type: 'boolean')]
    private bool $alertOnAnomaly = true;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt    = new \DateTime();
        $this->updatedAt    = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }


    public function expectedWasteKg(float $inputKg): float
    {
        return round($inputKg * (1 - $this->rendimientoEsperadoPct / 100), 3);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
    public function setProduct(Product $p): self
    {
        $this->product = $p;
        return $this;
    }

    public function getRendimientoEsperadoPct(): int
    {
        return $this->rendimientoEsperadoPct;
    }
    public function setRendimientoEsperadoPct(int $v): self
    {
        $this->rendimientoEsperadoPct = $v;
        return $this;
    }


    public function isAlertOnAnomaly(): bool
    {
        return $this->alertOnAnomaly;
    }
    public function setAlertOnAnomaly(bool $v): self
    {
        $this->alertOnAnomaly = $v;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}
