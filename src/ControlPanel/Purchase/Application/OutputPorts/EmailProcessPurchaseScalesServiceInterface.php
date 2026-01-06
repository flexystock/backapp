<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\OutputPorts;

use App\Entity\Main\Client;
use App\Entity\Main\PurchaseScales;

interface EmailProcessPurchaseScalesServiceInterface
{
    public function sendScalesProcessingNotificationToClient(Client $client, PurchaseScales $purchaseScales): void;
}
