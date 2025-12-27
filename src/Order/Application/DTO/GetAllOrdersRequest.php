<?php

namespace App\Order\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetAllOrdersRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_UUID_CLIENT')]
    #[Assert\Uuid(message: 'INVALID_UUID_CLIENT')]
    private string $uuidClient;

    private ?string $status = null;
    
    private ?int $supplierId = null;

    public function __construct(
        string $uuidClient,
        ?string $status = null,
        ?int $supplierId = null
    ) {
        $this->uuidClient = $uuidClient;
        $this->status = $status;
        $this->supplierId = $supplierId;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getSupplierId(): ?int
    {
        return $this->supplierId;
    }
}
