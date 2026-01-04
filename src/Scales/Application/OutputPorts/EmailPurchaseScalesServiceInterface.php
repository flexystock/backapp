<?php

declare(strict_types=1);

namespace App\Scales\Application\OutputPorts;

use App\Entity\Main\Client;
use App\Entity\Main\PurchaseScales;

interface EmailPurchaseScalesServiceInterface
{
    /**
     * Send email to Flexystock notifying about a new scale purchase request.
     */
    public function sendPurchaseNotificationToFlexystock(PurchaseScales $purchaseScales): void;

    /**
     * Send email to client notifying that their scales are being created.
     */
    public function sendScalesProcessingNotificationToClient(Client $client, PurchaseScales $purchaseScales): void;
}
