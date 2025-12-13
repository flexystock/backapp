<?php

namespace App\Order\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateOrderStatusRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_ORDER_ID')]
    private int $orderId;

    #[Assert\NotBlank(message: 'REQUIRED_STATUS')]
    #[Assert\Choice(
        choices: ['draft', 'pending', 'sent', 'confirmed', 'received', 'cancelled'],
        message: 'INVALID_STATUS'
    )]
    private string $status;

    private ?int $changedByUserId = null;

    private ?string $notes = null;

    private ?string $cancellationReason = null;

    public function __construct(
        int $orderId,
        string $status,
        ?int $changedByUserId = null,
        ?string $notes = null,
        ?string $cancellationReason = null
    ) {
        $this->orderId = $orderId;
        $this->status = $status;
        $this->changedByUserId = $changedByUserId;
        $this->notes = $notes;
        $this->cancellationReason = $cancellationReason;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getChangedByUserId(): ?int
    {
        return $this->changedByUserId;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }
}
