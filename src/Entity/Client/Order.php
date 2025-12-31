<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'orders')]
#[ORM\UniqueConstraint(name: 'unique_order_number', columns: ['order_number'])]
#[ORM\Index(name: 'idx_status', columns: ['status'])]
#[ORM\Index(name: 'idx_delivery_date', columns: ['delivery_date'])]
#[ORM\Index(name: 'idx_created_at', columns: ['created_at'])]
#[ORM\Index(name: 'idx_supplier_status', columns: ['client_supplier_id', 'status'])]
class Order
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(name: 'order_number', type: 'string', length: 50)]
    private string $orderNumber;

    #[ORM\Column(name: 'client_supplier_id', type: 'integer')]
    private int $clientSupplierId;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = 'draft';

    #[ORM\Column(name: 'total_amount', type: 'decimal', precision: 10, scale: 2)]
    private float $totalAmount = 0.00;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = 'EUR';

    #[ORM\Column(name: 'delivery_date', type: 'date', nullable: true)]
    private ?\DateTimeInterface $deliveryDate = null;

    #[ORM\Column(name: 'sent_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $sentAt = null;

    #[ORM\Column(name: 'confirmed_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $confirmedAt = null;

    #[ORM\Column(name: 'received_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $receivedAt = null;

    #[ORM\Column(name: 'cancelled_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $cancelledAt = null;

    #[ORM\Column(name: 'email_sent_to', type: 'string', length: 255, nullable: true)]
    private ?string $emailSentTo = null;

    #[ORM\Column(name: 'pdf_path', type: 'string', length: 500, nullable: true)]
    private ?string $pdfPath = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(name: 'cancellation_reason', type: 'text', nullable: true)]
    private ?string $cancellationReason = null;

    #[ORM\Column(name: 'created_by_user_id', type: 'integer', nullable: true)]
    private ?int $createdByUserId = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getClientSupplierId(): int
    {
        return $this->clientSupplierId;
    }

    public function setClientSupplierId(int $clientSupplierId): self
    {
        $this->clientSupplierId = $clientSupplierId;
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

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTimeInterface $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;
        return $this;
    }

    public function getReceivedAt(): ?\DateTimeInterface
    {
        return $this->receivedAt;
    }

    public function setReceivedAt(?\DateTimeInterface $receivedAt): self
    {
        $this->receivedAt = $receivedAt;
        return $this;
    }

    public function getCancelledAt(): ?\DateTimeInterface
    {
        return $this->cancelledAt;
    }

    public function setCancelledAt(?\DateTimeInterface $cancelledAt): self
    {
        $this->cancelledAt = $cancelledAt;
        return $this;
    }

    public function getEmailSentTo(): ?string
    {
        return $this->emailSentTo;
    }

    public function setEmailSentTo(?string $emailSentTo): self
    {
        $this->emailSentTo = $emailSentTo;
        return $this;
    }

    public function getPdfPath(): ?string
    {
        return $this->pdfPath;
    }

    public function setPdfPath(?string $pdfPath): self
    {
        $this->pdfPath = $pdfPath;
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

    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?string $cancellationReason): self
    {
        $this->cancellationReason = $cancellationReason;
        return $this;
    }

    public function getCreatedByUserId(): ?int
    {
        return $this->createdByUserId;
    }

    public function setCreatedByUserId(?int $createdByUserId): self
    {
        $this->createdByUserId = $createdByUserId;
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

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
