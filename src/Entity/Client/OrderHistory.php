<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'order_history')]
#[ORM\Index(name: 'idx_order_id', columns: ['order_id'])]
#[ORM\Index(name: 'idx_created_at', columns: ['created_at'])]
class OrderHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(name: 'order_id', type: 'integer')]
    private int $orderId;

    #[ORM\Column(name: 'status_from', type: 'string', length: 20, nullable: true)]
    private ?string $statusFrom = null;

    #[ORM\Column(name: 'status_to', type: 'string', length: 20)]
    private string $statusTo;

    #[ORM\Column(name: 'changed_by_user_id', type: 'integer', nullable: true)]
    private ?int $changedByUserId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getStatusFrom(): ?string
    {
        return $this->statusFrom;
    }

    public function setStatusFrom(?string $statusFrom): self
    {
        $this->statusFrom = $statusFrom;
        return $this;
    }

    public function getStatusTo(): string
    {
        return $this->statusTo;
    }

    public function setStatusTo(string $statusTo): self
    {
        $this->statusTo = $statusTo;
        return $this;
    }

    public function getChangedByUserId(): ?int
    {
        return $this->changedByUserId;
    }

    public function setChangedByUserId(?int $changedByUserId): self
    {
        $this->changedByUserId = $changedByUserId;
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

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}
