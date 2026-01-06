<?php

declare(strict_types=1);

namespace App\Scales\Application\DTO;

class PurchaseScalesResponse
{
    private bool $success;
    private string $message;
    private ?string $uuidPurchase;
    private int $statusCode;

    public function __construct(bool $success, string $message, ?string $uuidPurchase = null, int $statusCode = 200)
    {
        $this->success = $success;
        $this->message = $message;
        $this->uuidPurchase = $uuidPurchase;
        $this->statusCode = $statusCode;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getUuidPurchase(): ?string
    {
        return $this->uuidPurchase;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'uuid_purchase' => $this->uuidPurchase,
        ];
    }
}
