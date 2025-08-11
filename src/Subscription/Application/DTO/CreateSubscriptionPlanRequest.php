<?php

namespace App\Subscription\Application\DTO;

class CreateSubscriptionPlanRequest
{
    private string $name;
    private string $description;
    private float $price;
    private int $maxScales;
    private ?string $uuidUser = null;

    public function __construct(string $name, string $description, float $price, int $maxScales, ?string $uuidUser = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->maxScales = $maxScales;
        $this->uuidUser = $uuidUser;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getMaxScales(): int
    {
        return $this->maxScales;
    }

    public function getUuidUser(): ?string
    {
        return $this->uuidUser;
    }

    public function setUuidUser(?string $uuidUser): void
    {
        $this->uuidUser = $uuidUser;
    }
}
