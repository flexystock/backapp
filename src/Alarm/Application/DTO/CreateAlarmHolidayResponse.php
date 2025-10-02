<?php

namespace App\Alarm\Application\DTO;

class CreateAlarmHolidayResponse
{
    /**
     * @var array<string, mixed>
     */
    private array $holiday;

    public function __construct(array $holiday)
    {
        $this->holiday = $holiday;
    }

    /**
     * @return array<string, mixed>
     */
    public function getHoliday(): array
    {
        return $this->holiday;
    }
}
