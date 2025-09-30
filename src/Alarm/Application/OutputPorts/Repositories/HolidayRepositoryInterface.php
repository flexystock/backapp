<?php

namespace App\Alarm\Application\OutputPorts\Repositories;

use App\Entity\Client\Holiday;

interface HolidayRepositoryInterface
{
    public function findByHolidayDate(\DateTimeInterface $holidayDate): ?Holiday;

    public function save(Holiday $holiday): void;

    public function remove(Holiday $holiday): void;

    public function flush(): void;
}
