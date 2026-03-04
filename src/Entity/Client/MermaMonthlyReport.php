<?php

namespace App\Entity\Client;

// ═══════════════════════════════════════════════════════
// MermaMonthlyReport — calculado por cron cada mes
// ═══════════════════════════════════════════════════════
#[ORM\Entity]
#[ORM\Table(name: 'merma_monthly_report')]
class MermaMonthlyReport
{
    /** Merma media del sector hostelería — usada para calcular el ahorro */
    const SECTOR_BASELINE_PCT = 8.0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Scale::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Scale $scale;

    /** Primer día del mes reportado. Ej: 2026-03-01 */
    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $periodMonth;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 3)]
    private float $inputKg = 0;          // Total reposiciones (kg)

    #[ORM\Column(type: 'decimal', precision: 10, scale: 3)]
    private float $consumedKg = 0;       // Consumo en horario servicio (kg)

    #[ORM\Column(type: 'decimal', precision: 10, scale: 3)]
    private float $anomalyKg = 0;        // Consumo fuera de horario (kg)

    #[ORM\Column(type: 'decimal', precision: 10, scale: 3)]
    private float $stockStartKg = 0;     // Stock inicio de mes (kg)

    #[ORM\Column(type: 'decimal', precision: 10, scale: 3)]
    private float $stockEndKg = 0;       // Stock fin de mes (kg)

    #[ORM\Column(type: 'decimal', precision: 10, scale: 3)]
    private float $expectedWasteKg = 0;  // Merma operativa esperada (kg)

    #[ORM\Column(type: 'decimal', precision: 10, scale: 3)]
    private float $actualWasteKg = 0;    // Merma real calculada (kg)

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $wasteCostEuros = 0;   // actualWasteKg × precio_compra

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private float $wastePct = 0;         // actualWasteKg / inputKg × 100

    /** Euros ahorrados vs. merma media del sector (baseline 8%) */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $savedVsBaseline = 0;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $generatedAt;

    public function __construct()
    {
        $this->generatedAt = new \DateTime();
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isAboveBaseline(): bool
    {
        return $this->wastePct > self::SECTOR_BASELINE_PCT;
    }

    public function getWasteStatus(): string
    {
        if ($this->wastePct <= 3.0) {
            return 'excellent';
        }   // verde
        if ($this->wastePct <= 6.0) {
            return 'good';
        }        // amarillo
        if ($this->wastePct <= 8.0) {
            return 'warning';
        }     // naranja
        return 'critical';                                  // rojo
    }

    public function getPeriodLabel(): string
    {
        return $this->periodMonth->format('F Y'); // "Marzo 2026"
    }

    // ── Getters / Setters ────────────────────────────────────

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
    public function getScale(): Scale
    {
        return $this->scale;
    }
    public function setScale(Scale $s): self
    {
        $this->scale = $s;
        return $this;
    }
    public function getPeriodMonth(): \DateTimeInterface
    {
        return $this->periodMonth;
    }
    public function setPeriodMonth(\DateTimeInterface $d): self
    {
        $this->periodMonth = $d;
        return $this;
    }
    public function getInputKg(): float
    {
        return (float) $this->inputKg;
    }
    public function setInputKg(float $v): self
    {
        $this->inputKg = $v;
        return $this;
    }
    public function getConsumedKg(): float
    {
        return (float) $this->consumedKg;
    }
    public function setConsumedKg(float $v): self
    {
        $this->consumedKg = $v;
        return $this;
    }
    public function getAnomalyKg(): float
    {
        return (float) $this->anomalyKg;
    }
    public function setAnomalyKg(float $v): self
    {
        $this->anomalyKg = $v;
        return $this;
    }
    public function getStockStartKg(): float
    {
        return (float) $this->stockStartKg;
    }
    public function setStockStartKg(float $v): self
    {
        $this->stockStartKg = $v;
        return $this;
    }
    public function getStockEndKg(): float
    {
        return (float) $this->stockEndKg;
    }
    public function setStockEndKg(float $v): self
    {
        $this->stockEndKg = $v;
        return $this;
    }
    public function getExpectedWasteKg(): float
    {
        return (float) $this->expectedWasteKg;
    }
    public function setExpectedWasteKg(float $v): self
    {
        $this->expectedWasteKg = $v;
        return $this;
    }
    public function getActualWasteKg(): float
    {
        return (float) $this->actualWasteKg;
    }
    public function setActualWasteKg(float $v): self
    {
        $this->actualWasteKg = $v;
        return $this;
    }
    public function getWasteCostEuros(): float
    {
        return (float) $this->wasteCostEuros;
    }
    public function setWasteCostEuros(float $v): self
    {
        $this->wasteCostEuros = $v;
        return $this;
    }
    public function getWastePct(): float
    {
        return (float) $this->wastePct;
    }
    public function setWastePct(float $v): self
    {
        $this->wastePct = $v;
        return $this;
    }
    public function getSavedVsBaseline(): float
    {
        return (float) $this->savedVsBaseline;
    }
    public function setSavedVsBaseline(float $v): self
    {
        $this->savedVsBaseline = $v;
        return $this;
    }
    public function getGeneratedAt(): \DateTimeInterface
    {
        return $this->generatedAt;
    }
}
