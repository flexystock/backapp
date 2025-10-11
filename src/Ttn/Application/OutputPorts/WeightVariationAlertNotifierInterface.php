<?php

namespace App\Ttn\Application\OutputPorts;

use App\Ttn\Application\DTO\WeightVariationAlertNotification;

interface WeightVariationAlertNotifierInterface
{
    public function notify(WeightVariationAlertNotification $notification): void;
}
