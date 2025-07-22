<?php

namespace App\Subscription\Application\InputPorts;

use App\Subscription\Application\DTO\GetSubscriptionStripeLatestInvoiceRequest;
use App\Subscription\Application\DTO\GetSubscriptionStripeLatestInvoiceResponse;

interface GetSubscriptionStripeLatestInvoiceUseCaseInterface
{
    public function execute(GetSubscriptionStripeLatestInvoiceRequest $request): GetSubscriptionStripeLatestInvoiceResponse;
}