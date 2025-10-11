<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $uuid;

    #[ORM\Column(type: 'string', length: 50)]
    private string $name;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $ean = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $expiration_date = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $perishable = false;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true, options: ['unsigned' => true])]
    private ?float $stock = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 5, nullable: true, options: ['unsigned' => true])]
    private ?float $weight_range = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $name_unit1 = null;

    #[ORM\Column(type: 'decimal', precision: 9, scale: 5, nullable: true, options: ['unsigned' => true])]
    private ?float $weight_unit1 = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $name_unit2 = null;

    #[ORM\Column(type: 'decimal', precision: 9, scale: 5, nullable: true, options: ['unsigned' => true])]
    private ?float $weight_unit2 = null;

    #[ORM\Column(type: 'string', length: 1, options: ['default' => '0'])]
    private string $main_unit = '0';

    #[ORM\Column(type: 'decimal', precision: 9, scale: 5, options: ['unsigned' => true, 'default' => '0.00000'])]
    private float $tare = 0.00000;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, options: ['unsigned' => true, 'default' => '0.00'])]
    private float $sale_price = 0.00;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, options: ['unsigned' => true, 'default' => '0.00'])]
    private float $cost_price = 0.00;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $out_system_stock = null;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true, 'default' => 30])]
    private int $days_average_consumption = 30;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true, 'default' => 0])]
    private int $days_serve_order = 0;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuidUserCreation;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $datehourCreation;

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    private ?string $uuidUserModification = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $datehourModification = null;

    #[ORM\Column(name: 'min_percentage', type: 'integer', options: ['unsigned' => true, 'default' => 0])]
    private int $minPercentage = 0;

    // Getters y Setters

    /**
     * Get identifier of the product.
     *
     * @return int product id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get UUID of the product.
     *
     * @return string product UUID
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * Set UUID of the product.
     *
     * @param string $uuid product UUID
     */
    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function getExpirationDate(): ?DateTime
    {
        return $this->expiration_date;
    }

    public function setExpirationDate(?DateTime $expiration_date): void
    {
        $this->expiration_date = $expiration_date;
    }

    public function getPerishable(): ?bool
    {
        return $this->perishable;
    }

    public function setPerishable(?bool $perishable): void
    {
        $this->perishable = $perishable;
    }

    public function setEan(?string $ean): self
    {
        $this->ean = $ean;

        return $this;
    }

    public function getStock(): ?float
    {
        return $this->stock;
    }

    public function setStock(?float $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getWeightRange(): ?float
    {
        return $this->weight_range;
    }

    public function setWeightRange(?float $weightRange): self
    {
        $this->weight_range = $weightRange;

        return $this;
    }

    public function getNameUnit1(): ?string
    {
        return $this->name_unit1;
    }

    public function setNameUnit1(?string $nameUnit1): self
    {
        $this->name_unit1 = $nameUnit1;

        return $this;
    }

    public function getWeightUnit1(): ?float
    {
        return $this->weight_unit1;
    }

    public function setWeightUnit1(?float $weightUnit1): self
    {
        $this->weight_unit1 = $weightUnit1;

        return $this;
    }

    public function getNameUnit2(): ?string
    {
        return $this->name_unit2;
    }

    public function setNameUnit2(?string $nameUnit2): self
    {
        $this->name_unit2 = $nameUnit2;

        return $this;
    }

    public function getWeightUnit2(): ?float
    {
        return $this->weight_unit2;
    }

    public function setWeightUnit2(?float $weightUnit2): self
    {
        $this->weight_unit2 = $weightUnit2;

        return $this;
    }

    public function getMainUnit(): string
    {
        return $this->main_unit;
    }

    public function setMainUnit(string $mainUnit): self
    {
        $this->main_unit = $mainUnit;

        return $this;
    }

    public function getTare(): float
    {
        return $this->tare;
    }

    public function setTare(float $tare): self
    {
        $this->tare = $tare;

        return $this;
    }

    public function getSalePrice(): float
    {
        return $this->sale_price;
    }

    public function setSalePrice(float $salePrice): self
    {
        $this->sale_price = $salePrice;

        return $this;
    }

    public function getCostPrice(): float
    {
        return $this->cost_price;
    }

    public function setCostPrice(float $costPrice): self
    {
        $this->cost_price = $costPrice;

        return $this;
    }

    public function getOutSystemStock(): ?bool
    {
        return $this->out_system_stock;
    }

    public function setOutSystemStock(?bool $outSystemStock): self
    {
        $this->out_system_stock = $outSystemStock;

        return $this;
    }

    public function getDaysAverageConsumption(): int
    {
        return $this->days_average_consumption;
    }

    public function setDaysAverageConsumption(int $daysAverageConsumption): self
    {
        $this->days_average_consumption = $daysAverageConsumption;

        return $this;
    }

    public function getDaysServeOrder(): int
    {
        return $this->days_serve_order;
    }

    public function setDaysServeOrder(int $daysServeOrder): self
    {
        $this->days_serve_order = $daysServeOrder;

        return $this;
    }

    public function getUuidUserCreation(): string
    {
        return $this->uuidUserCreation;
    }

    public function setUuidUserCreation(string $uuidUserCreation): self
    {
        $this->uuidUserCreation = $uuidUserCreation;

        return $this;
    }

    public function getDatehourCreation(): \DateTimeInterface
    {
        return $this->datehourCreation;
    }

    public function setDatehourCreation(\DateTimeInterface $datehourCreation): self
    {
        $this->datehourCreation = $datehourCreation;

        return $this;
    }

    public function getUuidUserModification(): ?string
    {
        return $this->uuidUserModification;
    }

    public function setUuidUserModification(?string $uuidUserModification): self
    {
        $this->uuidUserModification = $uuidUserModification;

        return $this;
    }

    public function getDatehourModification(): ?\DateTimeInterface
    {
        return $this->datehourModification;
    }

    public function setDatehourModification(?\DateTimeInterface $datehourModification): self
    {
        $this->datehourModification = $datehourModification;

        return $this;
    }

    public function getMinPercentage(): int
    {
        return $this->minPercentage;
    }

    public function setMinPercentage(int $minPercentage): self
    {
        $this->minPercentage = $minPercentage;

        return $this;
    }
}
