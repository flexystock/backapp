<?php

namespace App\Product\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateProductRequest
{
    #[Assert\Uuid(message: 'REQUIRED_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\Uuid(message: 'REQUIRED_PRODUCT_ID')]
    private string $uuidProduct;

    #[Assert\NotBlank(message: 'REQUIRED_NAME')]
    private string $name;

    private ?string $ean;

    private ?DateTime $expiration_date;

    private ?bool $perishable;

    #[Assert\Type(type: 'numeric', message: 'INVALID_STOCK')]
    private ?float $stock;

    private ?float $weightRange;

    private ?string $nameUnit1;

    private ?float $weightUnit1;

    private ?string $nameUnit2;

    private ?float $weightUnit2;

    #[Assert\Choice(
        choices: [0, 1, 2],
        message: 'INVALID_UNIT'
    )]
    private int $mainUnit;

    #[Assert\Type(type: 'numeric', message: 'INVALID_TARE')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'La tara no puede ser negativa.')]
    private ?float $tare;

    #[Assert\Type(type: 'numeric', message: 'INVALID_SALE_PRICE')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'El precio de venta no puede ser negativo.')]
    private float $salePrice;

    #[Assert\Type(type: 'numeric', message: 'INVALID_COST_PRICE')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'El precio de costo no puede ser negativo.')]
    private ?float $costPrice;

    private ?bool $outSystemStock;

    #[Assert\Type(type: 'integer', message: 'INVALID_DAYS_AVERAGE_CONSUMPTION')]
    #[Assert\GreaterThanOrEqual(value: 1, message: 'Debe haber al menos un día promedio de consumo.')]
    private ?int $daysAverageConsumption;

    #[Assert\Type(type: 'integer', message: 'INVALID_DAYS_SERVE_ORDER')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'Los días para servir el pedido no pueden ser negativos.')]
    private ?int $daysServeOrder;

    private string $uuidUserModification;
    private \DateTimeInterface $datehourModification;

    public function __construct(
        string $uuidClient,
        string $uuidProduct,
        string $name,
        ?string $ean = null,
        ?DateTime $expiration_date = null,
        ?bool $perishable = false,
        ?float $stock = 0.00,
        ?float $weightRange = null,
        ?string $nameUnit1 = null,
        ?float $weightUnit1 = null,
        ?string $nameUnit2 = null,
        ?float $weightUnit2 = null,
        ?int $mainUnit = 0,
        ?float $tare = 0.00,
        ?float $salePrice = 0.00,
        ?float $costPrice = 0.00,
        ?bool $outSystemStock = null,
        ?int $daysAverageConsumption = 1,
        ?int $daysServeOrder = 0,
    ) {
        $this->uuidClient = $uuidClient;
        $this->uuidProduct = $uuidProduct;
        $this->name = $name;
        $this->ean = $ean;
        $this->expiration_date = $expiration_date;
        $this->perishable = $perishable ?? false;
        $this->stock = $stock ?? 0.00;
        $this->weightRange = $weightRange;
        $this->nameUnit1 = $nameUnit1;
        $this->weightUnit1 = $weightUnit1;
        $this->nameUnit2 = $nameUnit2;
        $this->weightUnit2 = $weightUnit2;
        $this->mainUnit = $mainUnit ?? 0;
        $this->tare = $tare ?? 0.0;
        $this->salePrice = $salePrice ?? 0.00;
        $this->costPrice = $costPrice ?? 0.00;
        $this->outSystemStock = $outSystemStock;
        $this->daysAverageConsumption = $daysAverageConsumption ?? 30;
        $this->daysServeOrder = $daysServeOrder ?? 0;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUuidProduct(): string
    {
        return $this->uuidProduct;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function getExpirationDate(): ?DateTime
    {
        return $this->expiration_date;
    }

    public function getPerishable(): ?bool
    {
        return $this->perishable;
    }

    public function getStock(): ?float
    {
        return $this->stock;
    }

    public function getWeightRange(): ?float
    {
        return $this->weightRange;
    }

    public function getNameUnit1(): ?string
    {
        return $this->nameUnit1;
    }

    public function getWeightUnit1(): ?float
    {
        return $this->weightUnit1;
    }

    public function getNameUnit2(): ?string
    {
        return $this->nameUnit2;
    }

    public function getWeightUnit2(): ?float
    {
        return $this->weightUnit2;
    }

    public function getMainUnit(): ?int
    {
        return $this->mainUnit;
    }

    public function getTare(): ?float
    {
        return $this->tare;
    }

    public function getSalePrice(): ?float
    {
        return $this->salePrice;
    }

    public function getCostPrice(): ?float
    {
        return $this->costPrice;
    }

    public function getOutSystemStock(): ?bool
    {
        return $this->outSystemStock;
    }

    public function getDaysAverageConsumption(): ?int
    {
        return $this->daysAverageConsumption;
    }

    public function getDaysServeOrder(): ?int
    {
        return $this->daysServeOrder;
    }

    public function getUuidUserModification(): string
    {
        return $this->uuidUserModification;
    }

    public function getDatehourModification(): \DateTimeInterface
    {
        return $this->datehourModification;
    }

    public function setDatehourModification(\DateTimeInterface $datehourModification): void
    {
        $this->datehourModification = $datehourModification;
    }

    public function setUuidUserModification(string $uuidUserModification): void
    {
        $this->uuidUserModification = $uuidUserModification;
    }
}
