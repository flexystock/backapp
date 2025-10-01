<?php

namespace App\Alarm\Application\DTO;

class GetBusinessHoursResponse
{
    /**
     * @param array<int, array<string, mixed>> $businessHours
     */
    public function __construct(private readonly array $businessHours)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getBusinessHours(): array
    {
        return $this->businessHours;
    }
}
