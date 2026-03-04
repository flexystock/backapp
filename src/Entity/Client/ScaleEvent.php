<?php

namespace App\Entity\Client;

use App\Repository\ScaleEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScaleEventRepository::class)]
#[ORM\Table(name: 'scale_event')]
#[ORM\Index(name: 'idx_scale_event_anomalia', columns: ['type', 'is_confirmed'])]
#[ORM\Index(name: 'idx_scale_event_detected', columns: ['detected_at'])]
class ScaleEvent
{
    const TYPE_REPOSICION = 'reposicion';
    const TYPE_CONSUMO    = 'consumo';
    const TYPE_ANOMALIA   = 'anomalia';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Scale::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Scale $scale;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('reposicion','consumo','anomalia')")]
    private string $type;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 3)]
    private float $weightBefore;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 3)]
    private float $weightAfter;

    /** Positivo = reposición, Negativo = consumo/anomalía */
    #[ORM\Column(type: 'decimal', precision: 8, scale: 3)]
    private float $deltaKg;

    /** delta_kg × precio_compra del producto */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?float $deltaCost = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $detectedAt;

    /** NULL = pendiente revisión, true = confirmado, false = descartado */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isConfirmed = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $confirmedAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $notes = null;

    // ── Getters / Setters ────────────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScale(): Scale
    {
        return $this->scale;
    }
    public function setScale(Scale $scale): self
    {
        $this->scale = $scale;
        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getWeightBefore(): float
    {
        return (float) $this->weightBefore;
    }
    public function setWeightBefore(float $w): self
    {
        $this->weightBefore = $w;
        return $this;
    }

    public function getWeightAfter(): float
    {
        return (float) $this->weightAfter;
    }
    public function setWeightAfter(float $w): self
    {
        $this->weightAfter = $w;
        return $this;
    }

    public function getDeltaKg(): float
    {
        return (float) $this->deltaKg;
    }
    public function setDeltaKg(float $d): self
    {
        $this->deltaKg = $d;
        return $this;
    }

    public function getDeltaCost(): ?float
    {
        return $this->deltaCost !== null ? (float) $this->deltaCost : null;
    }
    public function setDeltaCost(?float $c): self
    {
        $this->deltaCost = $c;
        return $this;
    }

    public function getDetectedAt(): \DateTimeInterface
    {
        return $this->detectedAt;
    }
    public function setDetectedAt(\DateTimeInterface $d): self
    {
        $this->detectedAt = $d;
        return $this;
    }

    public function getIsConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }
    public function setIsConfirmed(?bool $v): self
    {
        $this->isConfirmed = $v;
        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }
    public function setConfirmedAt(?\DateTimeInterface $d): self
    {
        $this->confirmedAt = $d;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
    public function setNotes(?string $n): self
    {
        $this->notes = $n;
        return $this;
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isAnomalia(): bool
    {
        return $this->type === self::TYPE_ANOMALIA;
    }
    public function isReposicion(): bool
    {
        return $this->type === self::TYPE_REPOSICION;
    }
    public function isConsumo(): bool
    {
        return $this->type === self::TYPE_CONSUMO;
    }
    public function isPendingReview(): bool
    {
        return $this->isAnomalia() && $this->isConfirmed === null;
    }
}
