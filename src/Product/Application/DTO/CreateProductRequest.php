<?php

namespace App\Product\Application\DTO;

class CreateProductRequest
{
    private string $uuidUserCreation;
    private \DateTimeInterface $datehourCreation;
    private string $name;
    private ?string $ean;
    private ?float $weightRange;
    private ?string $nameUnit1;
    private ?float $weightUnit1;
    private ?string $nameUnit2;
    private ?float $weightUnit2;
    private string $mainUnit; // 0,1,2
    private float $tare;
    private float $salePrice;
    private float $costPrice;
    private ?bool $outSystemStock;
    private int $daysAverageConsumption;
    private int $daysServeOrder;

    // Agrega el resto de campos si es necesario...
    // uuidUserCreation, datehourCreation y demás suelen ser automáticos o proceden de la lógica interna
    // (por ejemplo, uuidUserCreation podría venir del usuario autenticado y datehourCreation del momento actual).

    public function __construct(
        string $uuidClient,
        string $name,
        ?int $mainUnit = null,
        ?float $salePrice = null,
        ?float $costPrice = null,
        ?float $tare = null,
        ?int $daysServeOrder = null,
        ?int $daysAverageConsumption = null,
        ?string $ean = null,
        ?float $weightRange = null,
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
