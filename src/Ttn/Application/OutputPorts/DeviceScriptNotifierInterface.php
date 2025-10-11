<?php

namespace App\Ttn\Application\OutputPorts;

interface DeviceScriptNotifierInterface
{
    public function notify(string $deviceId, string $devEui, string $joinEui, string $appKey): void;
}
