<?php

namespace App\Entity\Client;

// asumiendo que la clase se llama Scale
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Client\Scales;

/**
 * Representa la tabla weights_log, donde se registran los históricos de pesadas.
 */
#[ORM\Entity]
#[ORM\Table(name: 'weights_log')]
class WeightsLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    // Relación con Scale (ManyToOne => muchos weights_log pertenecen a 1 scale)
    #[ORM\ManyToOne(targetEntity: Scales::class)]
    #[ORM\JoinColumn(name: 'scale_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Scales $scale = null;

    // Relación con Product (opcional, ManyToOne)
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Product $product = null;

    /**
     * Fecha y hora del registro de la pesada.
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date;

    /**
     * Peso real medido.
     */
    #[ORM\Column(name: 'real_weight', type: 'decimal', precision: 8, scale: 5)]
    private float $real_weight;

    /**
     * Peso ajustado, si se aplica algún factor de corrección.
     */
    #[ORM\Column(name: 'adjust_weight', type: 'decimal', precision: 8, scale: 5)]
    private float $adjust_weight;

    /**
     * Porcentaje de carga, si se usa para indicar la ocupación respecto al máximo.
     */
    #[ORM\Column(name: 'charge_percentage', type: 'decimal', precision: 5, scale: 2)]
    private float $charge_percentage;

    /**
     * Voltaje medido en la báscula (o dispositivo).
     */
    #[ORM\Column(name: 'voltage', type: 'decimal', precision: 5, scale: 3)]
    private float $voltage;

    // ----------------------------
    // GETTERS y SETTERS (o usa DTO/constructor)
    // ----------------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScale(): ?Scales
    {
        return $this->scale;
    }

    public function setScale(?Scales $scale): self
    {
        $this->scale = $scale;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRealWeight(): float
    {
        return $this->real_weight;
    }

    public function setRealWeight(float $realWeight): self
    {
        $this->real_weight = $realWeight;

        return $this;
    }

    public function getAdjustWeight(): float
    {
        return $this->adjust_weight;
    }

    public function setAdjustWeight(float $adjustWeight): self
    {
        $this->adjust_weight = $adjustWeight;

        return $this;
    }

    public function getChargePercentage(): float
    {
        return $this->charge_percentage;
    }

    public function setChargePercentage(float $chargePercentage): self
    {
        $this->charge_percentage = $chargePercentage;

        return $this;
    }

    public function getVoltage(): float
    {
        return $this->voltage;
    }

    public function setVoltage(float $voltage): self
    {
        $this->voltage = $voltage;

        return $this;
    }


    public function getProductId(): ?int
    {
        return $this->product ? $this->product->getId() : null;
    }

    public function getScaleId(): ?int
    {
        return $this->scale ? $this->scale->getId() : null;
    }
}
