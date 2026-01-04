<?php

declare(strict_types=1);

namespace App\Entity\Main;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'purchase_scales')]
#[ORM\Index(name: 'idx_uuid_client', columns: ['uuid_client'])]
#[ORM\Index(name: 'idx_status', columns: ['status'])]
#[ORM\Index(name: 'idx_purchase_at', columns: ['purchase_at'])]
class PurchaseScales
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'uuid_purchase', type: Types::STRING, length: 36, unique: true)]
    private string $uuid_purchase;

    #[ORM\Column(name: 'uuid_client', type: Types::STRING, length: 36)]
    private string $uuid_client;

    #[ORM\Column(name: 'client_name', type: Types::STRING, length: 100)]
    private string $client_name;

    #[ORM\Column(name: 'quantity', type: Types::SMALLINT, options: ['unsigned' => true, 'default' => 1])]
    private int $quantity = 1;

    #[ORM\Column(
        name: 'status',
        type: Types::STRING,
        length: 20,
        columnDefinition: "ENUM('pending', 'processing', 'completed', 'rejected') NOT NULL DEFAULT 'pending'"
    )]
    private string $status = 'pending';

    #[ORM\Column(name: 'notes', type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: 'purchase_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $purchase_at;

    #[ORM\Column(name: 'processed_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $processed_at = null;

    #[ORM\Column(name: 'processed_by_uuid_user', type: Types::STRING, length: 36, nullable: true)]
    private ?string $processed_by_uuid_user = null;

    public function __construct()
    {
        $this->uuid_purchase = Uuid::v4()->toRfc4122();
        $this->purchase_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuidPurchase(): string
    {
        return $this->uuid_purchase;
    }

    public function setUuidPurchase(string $uuid_purchase): self
    {
        $this->uuid_purchase = $uuid_purchase;

        return $this;
    }

    public function getUuidClient(): string
    {
        return $this->uuid_client;
    }

    public function setUuidClient(string $uuid_client): self
    {
        $this->uuid_client = $uuid_client;

        return $this;
    }

    public function getClientName(): string
    {
        return $this->client_name;
    }

    public function setClientName(string $client_name): self
    {
        $this->client_name = $client_name;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function getPurchaseAt(): \DateTimeImmutable
    {
        return $this->purchase_at;
    }

    public function setPurchaseAt(\DateTimeImmutable $purchase_at): self
    {
        $this->purchase_at = $purchase_at;

        return $this;
    }

    public function getProcessedAt(): ?\DateTimeImmutable
    {
        return $this->processed_at;
    }

    public function setProcessedAt(?\DateTimeImmutable $processed_at): self
    {
        $this->processed_at = $processed_at;

        return $this;
    }

    public function getProcessedByUuidUser(): ?string
    {
        return $this->processed_by_uuid_user;
    }

    public function setProcessedByUuidUser(?string $processed_by_uuid_user): self
    {
        $this->processed_by_uuid_user = $processed_by_uuid_user;

        return $this;
    }
}
