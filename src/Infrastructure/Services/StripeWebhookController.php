<?php

namespace App\Infrastructure\Services;

use App\Subscription\Application\Services\SubscriptionWebhookService;
use Psr\Log\LoggerInterface;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeWebhookController
{
    private string $stripeWebhookSecret;
    private SubscriptionWebhookService $subscriptionWebhookService;

    public function __construct(
        string $stripeWebhookSecret,
        SubscriptionWebhookService $subscriptionWebhookService
    ) {
        $this->stripeWebhookSecret = $stripeWebhookSecret;
        $this->subscriptionWebhookService = $subscriptionWebhookService;
    }

    #[Route('/api/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function __invoke(Request $request, LoggerInterface $logger): Response
    {
        $payload = $request->getContent();
        $sig_header = $request->headers->get('stripe-signature');
        $secret = $this->stripeWebhookSecret;

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $secret);
            $logger->info('Stripe webhook received', ['type' => $event->type]);
        } catch (\Throwable $e) {
            $logger->error('Stripe webhook error', ['exception' => $e->getMessage()]);

            return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
        }

        $logger->info('Stripe event received:', ['type' => $event->type]);

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $checkoutSession = $event->data->object;
                    $this->subscriptionWebhookService->handleCheckoutCompleted($checkoutSession);
                    $logger->info('Checkout session completed processed', [
                        'session_id' => $checkoutSession->id,
                    ]);
                    break;

                case 'customer.subscription.deleted':
                    $subscription = $event->data->object;
                    $this->subscriptionWebhookService->handleSubscriptionDeleted($subscription);
                    $logger->info('Subscription deletion processed', [
                        'stripe_subscription_id' => $subscription->id,
                    ]);
                    break;

                default:
                    // $logger->info('Unhandled Stripe event type', ['type' => $event->type]);
                    break;
            }
        } catch (\Throwable $e) {
            $logger->error('Error processing Stripe webhook', [
                'event_type' => $event->type,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return new Response('Webhook processing failed', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('OK', Response::HTTP_OK);
    }
}
