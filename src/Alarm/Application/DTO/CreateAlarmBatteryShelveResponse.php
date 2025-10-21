<?php

namespace App\Alarm\Application\DTO;

class CreateAlarmBatteryShelveResponse
{
    public function __construct(private readonly bool $checkBatteryShelve)
    {
    }

    public function isCheckBatteryShelveEnabled(): bool
    {
        return $this->checkBatteryShelve;
    }
}
