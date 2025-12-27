<?php

namespace App\Supplier\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateSupplierRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_NAME')]
    private string $name;

    #[Assert\NotBlank(message: 'REQUIRED_SLUG')]
    private string $slug;

    private ?string $logoUrl = null;

    private ?string $website = null;

    #[Assert\Choice(
        choices: ['mayorista', 'distribuidor', 'fabricante', 'marketplace'],
        message: 'INVALID_CATEGORY'
    )]
    private string $category = 'distribuidor';

    private string $country = 'ES';

    private ?string $coverageArea = null;

    private ?string $description = null;

    private bool $hasApiIntegration = false;

    private ?string $integrationType = null;

    private bool $isActive = true;

    private bool $featured = false;

    public function __construct(
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
