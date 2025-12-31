<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'suppliers')]
#[ORM\Index(name: 'idx_slug', columns: ['slug'])]
#[ORM\Index(name: 'idx_active', columns: ['is_active'])]
#[ORM\Index(name: 'idx_category', columns: ['category'])]
#[ORM\Index(name: 'idx_featured', columns: ['featured'])]
class Supplier
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $slug;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $logo_url = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $category = 'distribuidor';

    #[ORM\Column(type: 'string', length: 2)]
    private string $country = 'ES';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $coverage_area = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private bool $has_api_integration = false;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $integration_type = null;

    #[ORM\Column(type: 'boolean')]
    private bool $is_active = true;

    #[ORM\Column(type: 'boolean')]
    private bool $featured = false;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $created_at;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updated_at;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getLogoUrl(): ?string
    {
        return $this->logo_url;
    }

    public function setLogoUrl(?string $logo_url): self
    {
        $this->logo_url = $logo_url;
        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCoverageArea(): ?string
    {
        return $this->coverage_area;
    }

    public function setCoverageArea(?string $coverage_area): self
    {
        $this->coverage_area = $coverage_area;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function hasApiIntegration(): bool
    {
        return $this->has_api_integration;
    }

    public function setHasApiIntegration(bool $has_api_integration): self
    {
        $this->has_api_integration = $has_api_integration;
        return $this;
    }

    public function getIntegrationType(): ?string
    {
        return $this->integration_type;
    }

    public function setIntegrationType(?string $integration_type): self
    {
        $this->integration_type = $integration_type;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;
        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }
}
