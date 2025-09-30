<?php

namespace App\Alarm\Application\OutputPorts\Repositories;

use App\Entity\Client\BusinessHour;

interface BusinessHourRepositoryInterface
{
    public function findByDayOfWeek(int $dayOfWeek): ?BusinessHour;

    /**
     * @return array<int, BusinessHour>
     */
    public function findAll(): array;

    public function save(BusinessHour $businessHour): void;

    public function remove(BusinessHour $businessHour): void;

    public function flush(): void;
}
