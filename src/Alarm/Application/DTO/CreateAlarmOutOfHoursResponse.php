<?php

namespace App\Alarm\Application\DTO;

class CreateAlarmOutOfHoursResponse
{
    private string $uuidClient;

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $businessHours;

    public function __construct(string $uuidClient, array $businessHours)
    {
        $this->uuidClient = $uuidClient;
        $this->businessHours = $businessHours;
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
}
