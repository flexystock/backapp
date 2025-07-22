<?php

namespace App\Stripe\Infrastructure\OutputAdapters\Repositories;

use App\Client\Infrastructure\OutputAdapters\Repositories\ClientRepository;
use App\Service\Stripe\StripeClientFactory;
use App\Stripe\Application\OutputPorts\SetupIntentRepositoryInterface;

class SetupIntentRepository implements SetupIntentRepositoryInterface
{
    public function __construct(
        private ClientRepository $clientRepository,
        private StripeClientFactory $stripeClientFactory
    ) {
    }

    public function createSetupIntent(string $uuidClient): string
    {
        $client = $this->clientRepository->findOneBy(['uuid' => $uuidClient]);
        if (!$client || !$client->getStripeCustomerId()) {
            throw new \RuntimeException('Cliente no tiene Stripe Customer ID');
        }

        $stripe = $this->stripeClientFactory->get();
        $setupIntent = $stripe->setupIntents->create([
            'customer' => $client->getStripeCustomerId(),
        ]);

        return $setupIntent->client_secret;
    }
}
