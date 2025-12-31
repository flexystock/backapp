<?php

namespace App\Supplier\Application\DTO;

class UpdateSupplierRequest
{
    private int $id;
    private string $name;
    private string $slug;
    private ?string $logoUrl;
    private ?string $website;
    private string $category;
    private string $country;
    private ?string $coverageArea;
    private ?string $description;
    private bool $hasApiIntegration;
    private ?string $integrationType;
    private bool $isActive;
    private bool $featured;

    public function __construct(
        int $id,
        string $name,
        string $slug,
        ?string $logoUrl = null,
        ?string $website = null,
        string $category = 'distribuidor',
        string $country = 'ES',
        ?string $coverageArea = null,
        ?string $description = null,
        bool $hasApiIntegration = false,
        ?string $integrationType = null,
        bool $isActive = true,
        bool $featured = false
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->logoUrl = $logoUrl;
        $this->website = $website;
        $this->category = $category;
        $this->country = $country;
        $this->coverageArea = $coverageArea;
        $this->description = $description;
        $this->hasApiIntegration = $hasApiIntegration;
        $this->integrationType = $integrationType;
        $this->isActive = $isActive;
        $this->featured = $featured;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logoUrl;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCoverageArea(): ?string
    {
        return $this->coverageArea;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function hasApiIntegration(): bool
    {
        return $this->hasApiIntegration;
    }

    public function getIntegrationType(): ?string
    {
        return $this->integrationType;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }
}
