<?php

namespace App\Alarm\Application\DTO;

final class SyncAlarmHolidaysRequest
{
    /**
     * @param array<int, array{date: string, name?: ?string}> $holidays
     */
    public function __construct(
        public readonly string $uuidClient,
        public readonly array $holidays,
        public readonly string $uuidUser,
        private readonly int $checkHolidays,
    ) {}

    public function isCheckHolidaysEnabled(): bool
    {
        return 1 === $this->checkHolidays;
    }
}
