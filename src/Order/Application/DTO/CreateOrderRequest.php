<?php

namespace App\Order\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateOrderRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_ORDER_NUMBER')]
    private string $orderNumber;

    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_SUPPLIER_ID')]
    private int $clientSupplierId;

    private string $status = 'draft';

    private float $totalAmount = 0.00;

    private string $currency = 'EUR';

    private ?\DateTimeInterface $deliveryDate = null;

    private ?string $notes = null;

    private ?int $createdByUserId = null;

    private array $items = [];

    public function __construct(
        string $orderNumber,
        int $clientSupplierId,
        ?string $status = 'draft',
        ?float $totalAmount = 0.00,
        ?string $currency = 'EUR',
        ?\DateTimeInterface $deliveryDate = null,
        ?string $notes = null,
        ?int $createdByUserId = null,
        array $items = []
    ) {
        $this->orderNumber = $orderNumber;
        $this->clientSupplierId = $clientSupplierId;
        $this->status = $status ?? 'draft';
        $this->totalAmount = $totalAmount ?? 0.00;
        $this->currency = $currency ?? 'EUR';
        $this->deliveryDate = $deliveryDate;
        $this->notes = $notes;
        $this->createdByUserId = $createdByUserId;
        $this->items = $items;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getClientSupplierId(): int
    {
        return $this->clientSupplierId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getCreatedByUserId(): ?int
    {
        return $this->createdByUserId;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
