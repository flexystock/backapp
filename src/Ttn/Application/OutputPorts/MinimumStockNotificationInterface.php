<?php

namespace App\Ttn\Application\OutputPorts;

use App\Ttn\Application\DTO\MinimumStockNotification;

interface MinimumStockNotificationInterface
{
    public function notify(MinimumStockNotification $notification): void;
}
