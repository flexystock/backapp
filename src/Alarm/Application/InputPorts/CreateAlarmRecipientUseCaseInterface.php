<?php

namespace App\Alarm\Application\InputPorts;

use App\Alarm\Application\DTO\CreateAlarmRecipientRequest;
use App\Entity\Client\AlarmTypeRecipient;

interface CreateAlarmRecipientUseCaseInterface
{
    public function execute(CreateAlarmRecipientRequest $request): AlarmTypeRecipient;
}
