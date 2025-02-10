<?php

namespace App\Product\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProductRequest
{
    private string $uuidUserCreation;

    private \DateTimeInterface $datehourCreation;

    #[Assert\NotBlank(message: 'REQUIRED_NAME')]
    private string $name;

    #[Assert\Uuid(message: 'REQUIRED_CLIENT_ID')]
    private string $uuidClient;

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
    private float $tare;

    #[Assert\Type(type: 'numeric', message: 'INVALID_SALE_PRICE')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'El precio de venta no puede ser negativo.')]
    private float $salePrice;

    #[Assert\Type(type: 'numeric', message: 'INVALID_COST_PRICE')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'El precio de costo no puede ser negativo.')]
    private float $costPrice;

    private ?bool $outSystemStock;

    #[Assert\Type(type: 'integer', message: 'INVALID_DAYS_AVERAGE_CONSUMPTION')]
    #[Assert\GreaterThanOrEqual(value: 1, message: 'Debe haber al menos un día promedio de consumo.')]
    private int $daysAverageConsumption;

    #[Assert\Type(type: 'integer', message: 'INVALID_DAYS_SERVE_ORDER')]
    #[Assert\GreaterThanOrEqual(value: 0, message: 'Los días para servir el pedido no pueden ser negativos.')]
    private int $daysServeOrder;

    // Agrega el resto de campos si es necesario...
    // uuidUserCreation, datehourCreation y demás suelen ser automáticos o proceden de la lógica interna
    // (por ejemplo, uuidUserCreation podría venir del usuario autenticado y datehourCreation del momento actual).

    public function __construct(
        string $uuidClient,
        string $name,
        ?int $mainUnit = 0,
        ?float $salePrice = 0.00,
        ?float $costPrice = 0.00,
        ?float $tare = 0.00,
        ?int $daysServeOrder = 0,
        ?int $daysAverageConsumption = 1,
        ?string $ean = null,
        ?DateTime $expiration_date = null,
        ?bool $perishable = false,
        ?float $stock = 0.00,
        ?float $weightRange = 0.00,
        ?string $nameUnit1 = null,
        ?float $weightUnit1 = null,
        ?string $nameUnit2 = null,
        ?float $weightUnit2 = null,
        ?bool $outSystemStock = null,
    ) {
        $this->uuidClient = $uuidClient;
        $this->name = $name;
        $this->daysAverageConsumption = $daysAverageConsumption;
        $this->mainUnit = $mainUnit ?? 0;
        $this->tare = $tare ?? 0.0;
        $this->salePrice = $salePrice ?? 0.00;
        $this->costPrice = $costPrice ?? 0.00;
        $this->daysServeOrder = $daysServeOrder ?? 0;
        $this->daysAverageConsumption = $daysAverageConsumption ?? 30;
        $this->ean = $ean;
        $this->expiration_date = $expiration_date;
        $this->perishable = $perishable ?? false;
        $this->stock = $stock ?? 0.00;
        $this->weightRange = $weightRange ?? 0.00000;
        $this->nameUnit1 = $nameUnit1;
        $this->weightUnit1 = $weightUnit1 ?? 0.00000;
        $this->nameUnit2 = $nameUnit2;
        $this->weightUnit2 = $weightUnit2 ?? 0.00000;
        $this->outSystemStock = $outSystemStock ?? false;
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

    public function getMainUnit(): int
    {
        return $this->mainUnit;
    }

    public function getTare(): float
    {
        return $this->tare;
    }

    public function getSalePrice(): float
    {
        return $this->salePrice;
    }

    public function getCostPrice(): float
    {
        return $this->costPrice;
    }

    public function getOutSystemStock(): ?bool
    {
        return $this->outSystemStock;
    }

    public function getDaysAverageConsumption(): int
    {
        return $this->daysAverageConsumption;
    }

    public function getDaysServeOrder(): int
    {
        return $this->daysServeOrder;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function setUuidClient(string $uuidClient): void
    {
        $this->uuidClient = $uuidClient;
    }

    public function getUuidUserCreation(): ?string
    {
        return $this->uuidUserCreation;
    }

    public function setUuidUserCreation(?string $uuidUserCreation): void
    {
        $this->uuidUserCreation = $uuidUserCreation;
    }

    public function getDatehourCreation(): ?\DateTimeInterface
    {
        return $this->datehourCreation;
    }

    public function setDatehourCreation(?\DateTimeInterface $datehourCreation): void
    {
        $this->datehourCreation = $datehourCreation;
    }
}
