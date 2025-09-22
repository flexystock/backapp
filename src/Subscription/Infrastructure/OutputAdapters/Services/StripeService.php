<?php

namespace App\Subscription\Infrastructure\OutputAdapters\Services;

use App\Service\Stripe\StripeClientFactory;
use App\Subscription\Application\OutputPorts\StripeServiceInterface;
use Psr\Log\LoggerInterface;

class StripeService implements StripeServiceInterface
{
    public function __construct(
        private StripeClientFactory $stripeClientFactory,
        private LoggerInterface $logger
    ) {
    }

    public function createCheckoutSession(
        string $priceId,
        string $userEmail,
        string $clientUuid,
        string $planId,
        string $successUrl,
        string $cancelUrl
    ): array {
        try {
            $stripe = $this->stripeClientFactory->get();

            $session = $stripe->checkout->sessions->create([
                'mode' => 'subscription',
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                    ],
                ],
                'customer_email' => $userEmail,
                'client_reference_id' => $clientUuid,
                'metadata' => [
                    'plan_id' => $planId,
                    'client_uuid' => $clientUuid,
                ],
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);

            $this->logger->info('Stripe checkout session created successfully', [
                'session_id' => $session->id,
                'client_uuid' => $clientUuid,
                'plan_id' => $planId,
            ]);

            return [
                'url' => $session->url,
                'session_id' => $session->id,
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Error creating Stripe checkout session', [
                'exception' => $e->getMessage(),
                'client_uuid' => $clientUuid,
                'plan_id' => $planId,
                'price_id' => $priceId,
            ]);

            throw $e;
        }
    }
}
