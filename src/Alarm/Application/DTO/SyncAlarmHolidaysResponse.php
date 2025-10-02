<?php

namespace App\Alarm\Application\DTO;

final class SyncAlarmHolidaysResponse
{
    /**
     * @param array<int, array<string, mixed>> $holidays
     * @param array<int, array<string, mixed>> $created
     * @param array<int, array<string, mixed>> $updated
     * @param array<int, array<string, mixed>> $deleted
     */
    public function __construct(
        private array $holidays = [],
        private array $created  = [],
        private array $updated  = [],
        private array $deleted  = [],
    ) {}

    /** @return array<int, array<string, mixed>> */
    public function getHolidays(): array { return $this->holidays; }
    /** @return array<int, array<string, mixed>> */
    public function getCreated(): array  { return $this->created; }
    /** @return array<int, array<string, mixed>> */
    public function getUpdated(): array  { return $this->updated; }
    /** @return array<int, array<string, mixed>> */
    public function getDeleted(): array  { return $this->deleted; }
}