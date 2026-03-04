<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

// ═══════════════════════════════════════════════════════
// MermaConfig — configuración por producto
// ═══════════════════════════════════════════════════════
#[ORM\Entity]
#[ORM\Table(name: 'merma_config')]
#[ORM\HasLifecycleCallbacks]
class MermaConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    /**
     * De cada kg comprado, qué % se espera que llegue al plato.
     * El resto (100 - rendimiento) es merma operativa asumida.
     * Ej: 80 → de 1 kg comprado, 0,8 kg llegan al cliente.
     */
    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    private int $rendimientoEsperadoPct = 80;

    /** Inicio del horario de servicio — fuera de este rango las bajadas son anomalía */
    #[ORM\Column(type: 'time')]
    private \DateTimeInterface $serviceStart;

    #[ORM\Column(type: 'time')]
    private \DateTimeInterface $serviceEnd;

    /**
     * Delta mínimo en kg para registrar un evento.
     * Filtra oscilaciones normales del sensor (ruido).
     * Ej: 0.200 → solo registra cambios de más de 200g.
     */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 3)]
    private float $anomalyThresholdKg = 0.200;

    /** Si true, envía email al cliente cuando se detecta anomalía fuera de horario */
    #[ORM\Column(type: 'boolean')]
    private bool $alertOnAnomaly = true;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->serviceStart = new \DateTime('09:00');
        $this->serviceEnd   = new \DateTime('23:59');
        $this->createdAt    = new \DateTime();
        $this->updatedAt    = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    // ── Helpers ──────────────────────────────────────────────

    /**
     * ¿Una lectura a esta hora está dentro del horario de servicio?
     */
    public function isDuringService(\DateTimeInterface $datetime): bool
    {
        $time  = (int) $datetime->format('Hi'); // ej: 1430 = 14:30
        $start = (int) $this->serviceStart->format('Hi');
        $end   = (int) $this->serviceEnd->format('Hi');

        // Horario normal (no cruza medianoche)
        if ($start <= $end) {
            return $time >= $start && $time <= $end;
        }

        // Horario nocturno que cruza medianoche (ej: 22:00 → 02:00)
        return $time >= $start || $time <= $end;
    }

    /**
     * Merma operativa esperada para un input dado.
     * Ej: input=10 kg, rendimiento=80% → expected_waste = 2 kg
     */
    public function expectedWasteKg(float $inputKg): float
    {
        return round($inputKg * (1 - $this->rendimientoEsperadoPct / 100), 3);
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
    public function getRendimientoEsperadoPct(): int
    {
        return $this->rendimientoEsperadoPct;
    }
    public function setRendimientoEsperadoPct(int $v): self
    {
        $this->rendimientoEsperadoPct = $v;
        return $this;
    }
    public function getServiceStart(): \DateTimeInterface
    {
        return $this->serviceStart;
    }
    public function setServiceStart(\DateTimeInterface $d): self
    {
        $this->serviceStart = $d;
        return $this;
    }
    public function getServiceEnd(): \DateTimeInterface
    {
        return $this->serviceEnd;
    }
    public function setServiceEnd(\DateTimeInterface $d): self
    {
        $this->serviceEnd = $d;
        return $this;
    }
    public function getAnomalyThresholdKg(): float
    {
        return (float) $this->anomalyThresholdKg;
    }
    public function setAnomalyThresholdKg(float $v): self
    {
        $this->anomalyThresholdKg = $v;
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