<?php

namespace App\Alarm\Application\InputPorts;

interface DeleteAlarmRecipientUseCaseInterface
{
    public function execute(int $id, string $uuidClient): bool;
}
