<?php

namespace App\Alarm\Application\DTO;

class GetBusinessHoursResponse
{
    /**
     * @param array<int, array<string, mixed>> $businessHours
     */
    private int $checkoutOfHours;
    public function __construct(private readonly array $businessHours, int $checkoutOfHours)
    {
        $this->checkoutOfHours = $checkoutOfHours;
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
