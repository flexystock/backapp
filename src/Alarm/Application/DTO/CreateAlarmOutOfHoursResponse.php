<?php

namespace App\Alarm\Application\DTO;

class CreateAlarmOutOfHoursResponse
{
    private string $uuidClient;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $businessHours;

    private int $checkoutOfHours;

    public function __construct(string $uuidClient, array $businessHours, int $checkoutOfHours)
    {
        $this->uuidClient = $uuidClient;
        $this->businessHours = $businessHours;
        $this->checkoutOfHours = $checkoutOfHours;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getBusinessHours(): array
    {
        return $this->businessHours;
    }

    public function getCheckoutOfHours(): int
    {
        return $this->checkoutOfHours;
    }
}
