<?php

namespace App\Alarm\Application\OutputPorts\Repositories;

use App\Entity\Client\Holiday;

interface HolidayRepositoryInterface
{
    public function findByHolidayDate(\DateTimeInterface $holidayDate): ?Holiday;

    /**
     * @return array<int, Holiday>
     */
    public function findAll(): array;

    public function save(Holiday $holiday): void;

    public function remove(Holiday $holiday): void;

    public function flush(): void;
}
