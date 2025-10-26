<?php

namespace App\Alarm\Application\DTO;

class GetHolidaysResponse
{
    /**
     * @param array<int, array<string, mixed>> $holidays
     */
    private int $checkoutOfHolidays;
    public function __construct(private readonly array $holidays, int $checkoutOfHolidays)
    {
        $this->checkoutOfHolidays = $checkoutOfHolidays;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getHolidays(): array
    {
        return $this->holidays;
    }

    public function getCheckoutOfHolidays(): int
    {
        return $this->checkoutOfHolidays;
    }
}
