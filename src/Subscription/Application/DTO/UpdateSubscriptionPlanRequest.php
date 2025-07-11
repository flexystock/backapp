<?php

namespace App\Subscription\Application\DTO;

class UpdateSubscriptionPlanRequest
{
    private string $id;
    private ?string $name = null;
    private ?string $description = null;
    private ?float $price = null;
    private ?int $maxScales = null;
    private ?string $uuidUserModification = null;
    private ?\DateTimeInterface $datehourModification = null;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public function getMaxScales(): ?int
    {
        return $this->maxScales;
    }

    public function setMaxScales(?int $maxScales): void
    {
        $this->maxScales = $maxScales;
    }

    public function getUuidUserModification(): ?string
    {
        return $this->uuidUserModification;
    }

    public function setUuidUserModification(?string $uuidUserModification): void
    {
        $this->uuidUserModification = $uuidUserModification;
    }

    public function getDatehourModification(): ?\DateTimeInterface
    {
        return $this->datehourModification;
    }

    public function setDatehourModification(?\DateTimeInterface $datehourModification): void
    {
        $this->datehourModification = $datehourModification;
    }
}
