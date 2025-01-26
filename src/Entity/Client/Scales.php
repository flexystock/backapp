<?php

namespace App\Entity\Client;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Representa la tabla 'scales' en la BBDD del cliente.
 */
#[ORM\Entity]
#[ORM\Table(name: 'scales')]
class Scales
{
    /**
     * Clave primaria autoincremental.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private int $id;

    /**
     * UUID de la balanza (único).
     */
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $uuid;

    /**
     * ID del dispositivo en TTN (end_device_id).
     */
    #[ORM\Column(name: 'end_device_id', type: 'string', length: 50)]
    private string $end_device_id;

    /**
     * Voltaje mínimo.
     * DECIMAL(5,3) UNSIGNED NOT NULL DEFAULT '3.2'.
     */
    #[ORM\Column(name: 'voltage_min', type: 'decimal', precision: 5, scale: 3, options: ['unsigned' => true, 'default' => 3.2])]
    private float $voltage_min;

    /**
     * Porcentaje de carga, si se usa para indicar la ocupación respecto al máximo.
     */
    #[ORM\Column(name: 'voltage_percentage', type: 'decimal', precision: 5, scale: 2)]
    private float $voltage_percentage;

    /**
     * Último envío de la báscula.
     * Puede ser NULL => por eso ?DateTimeInterface.
     */
    #[ORM\Column(name: 'last_send', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $last_send = null;

    /**
     * Fecha estimada de fin de batería.
     * Puede ser NULL => por eso ?DateTimeInterface.
     */
    #[ORM\Column(name: 'battery_die', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $battery_die = null;

    /**
     * Relación ManyToOne con Product.
     * product_id -> FK
     * ON DELETE SET NULL => por eso 'nullable' => true.
     */
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'scales')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Product $product_id = null;

    /**
     * Posición X (posX).
     * TINYINT(2) UNSIGNED DEFAULT NULL.
     */
    #[ORM\Column(name: 'posX', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    private ?int $posX = null;

    /**
     * Ancho (width).
     * TINYINT(2) UNSIGNED DEFAULT NULL.
     */
    #[ORM\Column(name: 'width', type: 'smallint', nullable: true, options: ['unsigned' => true])]
    private ?int $width = null;

    /**
     * UUID del usuario que creó el registro.
     */
    #[ORM\Column(name: 'uuid_user_creation', type: 'string', length: 36)]
    private string $uuid_user_creation;

    /**
     * Fecha y hora de creación.
     */
    #[ORM\Column(name: 'datehour_creation', type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $datehour_creation;

    /**
     * UUID del usuario que modificó el registro.
     */
    #[ORM\Column(name: 'uuid_user_modification', type: 'string', length: 36, nullable: true)]
    private ?string $uuid_user_modification = null;

    /**
     * Fecha y hora de modificación.
     */
    #[ORM\Column(name: 'datehour_modification', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datehour_modification = null;

    // ================== GETTERS & SETTERS ==================

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getEndDeviceId(): string
    {
        return $this->end_device_id;
    }

    public function setEndDeviceId(string $endDeviceId): self
    {
        $this->end_device_id = $endDeviceId;

        return $this;
    }

    public function getVoltageMin(): float
    {
        return $this->voltage_min;
    }

    public function setVoltageMin(float $voltageMin): self
    {
        $this->voltage_min = $voltageMin;

        return $this;
    }

    public function getLastSend(): ?\DateTimeInterface
    {
        return $this->last_send;
    }

    public function setLastSend(?\DateTimeInterface $lastSend): self
    {
        $this->last_send = $lastSend;

        return $this;
    }

    public function getBatteryDie(): ?\DateTimeInterface
    {
        return $this->battery_die;
    }

    public function setBatteryDie(?\DateTimeInterface $batteryDie): self
    {
        $this->battery_die = $batteryDie;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product_id;
    }

    public function setProduct(?Product $product): self
    {
        $this->product_id = $product;

        return $this;
    }

    public function getPosX(): ?int
    {
        return $this->posX;
    }

    public function setPosX(?int $posX): self
    {
        $this->posX = $posX;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getUuidUserCreation(): string
    {
        return $this->uuid_user_creation;
    }

    public function setUuidUserCreation(string $uuidUserCreation): self
    {
        $this->uuid_user_creation = $uuidUserCreation;

        return $this;
    }

    public function getDatehourCreation(): \DateTimeInterface
    {
        return $this->datehour_creation;
    }

    public function setDatehourCreation(\DateTimeInterface $datehourCreation): self
    {
        $this->datehour_creation = $datehourCreation;

        return $this;
    }

    public function getUuidUserModification(): ?string
    {
        return $this->uuid_user_modification;
    }

    public function setUuidUserModification(?string $uuidUserModification): self
    {
        $this->uuid_user_modification = $uuidUserModification;

        return $this;
    }

    public function getDatehourModification(): ?\DateTimeInterface
    {
        return $this->datehour_modification;
    }

    public function setDatehourModification(?\DateTimeInterface $datehourModification): self
    {
        $this->datehour_modification = $datehourModification;

        return $this;
    }

    public function getVoltagePercentage(): float
    {
        return $this->voltage_percentage;
    }

    public function setVoltagePercentage(float $voltagePercentage): self
    {
        $this->voltage_percentage = $voltagePercentage;

        return $this;
    }
}
