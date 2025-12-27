<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'supplier_integrations')]
#[ORM\Index(name: 'idx_supplier_type', columns: ['supplier_id', 'integration_type'])]
class SupplierIntegration
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $supplier_id;

    #[ORM\Column(type: 'string', length: 20)]
    private string $integration_type;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $api_endpoint = null;

    #[ORM\Column(type: 'boolean')]
    private bool $api_key_required = false;

    #[ORM\Column(type: 'boolean')]
    private bool $webhook_support = false;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $documentation_url = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $setup_instructions = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $config_schema = null;

    #[ORM\Column(type: 'boolean')]
    private bool $is_active = true;

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

    public function getSupplierId(): int
    {
        return $this->supplier_id;
    }

    public function setSupplierId(int $supplier_id): self
    {
        $this->supplier_id = $supplier_id;
        return $this;
    }

    public function getIntegrationType(): string
    {
        return $this->integration_type;
    }

    public function setIntegrationType(string $integration_type): self
    {
        $this->integration_type = $integration_type;
        return $this;
    }

    public function getApiEndpoint(): ?string
    {
        return $this->api_endpoint;
    }

    public function setApiEndpoint(?string $api_endpoint): self
    {
        $this->api_endpoint = $api_endpoint;
        return $this;
    }

    public function isApiKeyRequired(): bool
    {
        return $this->api_key_required;
    }

    public function setApiKeyRequired(bool $api_key_required): self
    {
        $this->api_key_required = $api_key_required;
        return $this;
    }

    public function hasWebhookSupport(): bool
    {
        return $this->webhook_support;
    }

    public function setWebhookSupport(bool $webhook_support): self
    {
        $this->webhook_support = $webhook_support;
        return $this;
    }

    public function getDocumentationUrl(): ?string
    {
        return $this->documentation_url;
    }

    public function setDocumentationUrl(?string $documentation_url): self
    {
        $this->documentation_url = $documentation_url;
        return $this;
    }

    public function getSetupInstructions(): ?string
    {
        return $this->setup_instructions;
    }

    public function setSetupInstructions(?string $setup_instructions): self
    {
        $this->setup_instructions = $setup_instructions;
        return $this;
    }

    public function getConfigSchema(): ?array
    {
        return $this->config_schema;
    }

    public function setConfigSchema(?array $config_schema): self
    {
        $this->config_schema = $config_schema;
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
