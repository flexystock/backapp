<?php

namespace App\Alarm\Application\DTO;

class GetHolidaysResponse
{
    /**
     * @param array<int, array<string, mixed>> $holidays
     */
    public function __construct(private readonly array $holidays)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getHolidays(): array
    {
        return $this->holidays;
    }
}
