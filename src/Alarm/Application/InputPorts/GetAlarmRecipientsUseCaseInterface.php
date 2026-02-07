<?php

namespace App\Alarm\Application\InputPorts;

use App\Entity\Client\AlarmTypeRecipient;

interface GetAlarmRecipientsUseCaseInterface
{
    /**
     * @param string $uuidClient
     * @param int|null $alarmTypeId
     * @return AlarmTypeRecipient[]
     */
    public function execute(string $uuidClient, ?int $alarmTypeId = null): array;
}
