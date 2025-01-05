<?php

namespace App\Product\Application\DTO;

class UpdateProductRequest
{
    private string $uuidClient;
    private string $uuidProduct;
    private ?string $name;
    private ?string $ean;
    private ?float $weightRange;
    private ?string $nameUnit1;
    private ?float $weightUnit1;
    private ?string $nameUnit2;
    private ?float $weightUnit2;
    private ?int $mainUnit;
    private ?float $tare;
    private ?float $salePrice;
    private ?float $costPrice;
    private ?bool $outSystemStock;
    private ?int $daysAverageConsumption;
    private ?int $daysServeOrder;
    private string $uuidUserModification;
    private \DateTimeInterface $datehourModification;

    public function __construct(
        string $uuidClient,
        string $uuidProduct,
        string $name,
        ?string $ean = null,
        ?float $weightRange = null,
        ?string $nameUnit1 = null,
        ?float $weightUnit1 = null,
        ?string $nameUnit2 = null,
        ?float $weightUnit2 = null,
        ?int $mainUnit = null,
        ?float $tare = null,
        ?float $salePrice = null,
        ?float $costPrice = null,
        ?bool $outSystemStock = null,
        ?int $daysAverageConsumption = null,
        ?int $daysServeOrder = null,
    ) {
        $this->uuidClient = $uuidClient;
        $this->uuidProduct = $uuidProduct;
        $this->name = $name;
        $this->ean = $ean;
        $this->weightRange = $weightRange;
        $this->nameUnit1 = $nameUnit1;
        $this->weightUnit1 = $weightUnit1;
        $this->nameUnit2 = $nameUnit2;
        $this->weightUnit2 = $weightUnit2;
        $this->mainUnit = $mainUnit ?? 0;
        $this->tare = $tare;
        $this->salePrice = $salePrice;
        $this->costPrice = $costPrice;
        $this->outSystemStock = $outSystemStock;
        $this->daysAverageConsumption = $daysAverageConsumption;
        $this->daysServeOrder = $daysServeOrder;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUuidProduct(): string
    {
        return $this->uuidProduct;
    }

    public function getName(): ?string
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
