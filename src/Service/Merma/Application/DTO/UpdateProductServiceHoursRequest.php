<?php

namespace App\Service\Merma\Application\DTO;

class UpdateProductServiceHoursRequest
{
    private string $uuidClient;
    private int    $productId;

    /**
     * @var array<int, array{dayOfWeek: int, startTime1: string, endTime1: string, startTime2: ?string, endTime2: ?string}>
     */
    private array $hours;

    /**
     * @param array<int, array{dayOfWeek: int, startTime1: string, endTime1: string, startTime2: ?string, endTime2: ?string}> $hours
     */
    public function __construct(string $uuidClient, int $productId, array $hours)
    {
        $this->uuidClient = $uuidClient;
        $this->productId  = $productId;
        $this->hours      = $hours;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return array<int, array{dayOfWeek: int, startTime1: string, endTime1: string, startTime2: ?string, endTime2: ?string}>
     */
    public function getHours(): array
    {
        return $this->hours;
    }
}
