<?php

namespace App\Stripe\Infrastructure\OutputAdapters\Repositories;

use App\Client\Infrastructure\OutputAdapters\Repositories\ClientRepository;
use App\Service\Stripe\StripeClientFactory;
use App\Stripe\Application\OutputPorts\PaymentMethodUseRepositoryInterface;

class PaymentMethodRepository implements PaymentMethodUseRepositoryInterface
{
    public function __construct(
        private ClientRepository $clientRepository,
        private StripeClientFactory $stripeClientFactory
    ) {
    }

    public function getDefaultPaymentMethod(string $uuidClient): ?string
    {
        $client = $this->clientRepository->findOneBy(['uuid' => $uuidClient]);
        if (!$client || !$client->getStripeCustomerId()) {
            return null;
        }

        $stripe = $this->stripeClientFactory->get();
        $customer = $stripe->customers->retrieve($client->getStripeCustomerId(), []);
        return $customer->invoice_settings->default_payment_method ?? null;
    }
}
