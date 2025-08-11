<?php

namespace App\Infrastructure\Services;

use App\Entity\Main\Client;
use App\Entity\Main\PaymentTransaction;
use App\Entity\Main\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\StripeClient;

class PaymentGatewayService
{
    private EntityManagerInterface $entityManager;
    private StripeClient $stripe;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, StripeClient $stripe, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->stripe = $stripe;
        $this->logger = $logger;
    }

    public function charge(Subscription $subscription, float $amount, string $currency = 'EUR', string $gateway = 'stripe'): PaymentChargeResult
    {
        $transaction = new PaymentTransaction();
        $transaction->setSubscription($subscription);
        $transaction->setAmount($amount);
        $transaction->setCurrency($currency);
        $transaction->setGateway($gateway);
        $transaction->setStatus('pending');
        $transaction->setCreatedAt(new \DateTime());

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $clientSecret = null;

        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount' => (int) round($amount * 100),
                'currency' => strtolower($currency),
                'metadata' => [
                    'transaction_id' => $transaction->getId(),
                    'subscription_uuid' => $subscription->getUuidSubscription(),
                ],
            ]);

            $clientSecret = $intent->client_secret ?? null;

            $transaction->setTransactionReference($intent->id);

            if ('succeeded' === $intent->status) {
                $transaction->setStatus('paid');
                $subscription->setPaymentStatus('paid');
            } else {
                $transaction->setStatus('processing');
            }
        } catch (\Throwable $e) {
            $transaction->setStatus('failed');
            $subscription->setPaymentStatus('failed');
            $this->logger->error('Stripe payment error', ['exception' => $e]);
        }

        $this->entityManager->flush();

        return new PaymentChargeResult($transaction, $clientSecret);
    }

    public function createStripeSubscription(
        Subscription $subscription,
        \App\Entity\Main\SubscriptionPlan $plan,
        Client $client
    ): array {
        // 1. Asegúrate de que el cliente existe en Stripe
        if (!$client->getStripeCustomerId()) {
            $this->logger->info('Intentando crear cliente en Stripe', [
                'client_name' => $client->getName(),
                'client_email' => $client->getCompanyEmail(),
            ]);
            $stripeCustomer = $this->stripe->customers->create([
                'name' => $client->getName(),
                'email' => $client->getCompanyEmail() ?? 'fake_'.$client->getUuidClient().'@mail.com',
            ]);
            $this->logger->info('Respuesta de Stripe al crear cliente', [
                'stripe_customer' => (array) $stripeCustomer,
            ]);
            $client->setStripeCustomerId($stripeCustomer->id);
            $this->entityManager->flush();
        } else {
            $this->logger->info('Cliente ya existe en Stripe', [
                'customer_id' => $client->getStripeCustomerId(),
            ]);
        }

        // 2. Crea la suscripción en Stripe
        $stripeParams = [
            'customer' => $client->getStripeCustomerId(),
            'items' => [['price' => $plan->getStripePriceId()]],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
        ];
        $this->logger->info('Enviando a Stripe para crear suscripción', [
            'params' => $stripeParams,
        ]);

        try {
            $stripeSubscription = $this->stripe->subscriptions->create($stripeParams);
            $this->logger->info('Respuesta de Stripe al crear suscripción', [
                'stripe_subscription' => (array) $stripeSubscription,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Error al crear la suscripción en Stripe', [
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString(),
                'params' => $stripeParams,
            ]);
            throw $e;
        }

        $subscription->setStripeSubscriptionId($stripeSubscription->id);
        $this->entityManager->flush();

        // 3. Extrae el client_secret para el front
        $clientSecret = null;
        if (
            isset($stripeSubscription->latest_invoice) &&
            isset($stripeSubscription->latest_invoice->payment_intent) &&
            isset($stripeSubscription->latest_invoice->payment_intent->client_secret)
        ) {
            $clientSecret = $stripeSubscription->latest_invoice->payment_intent->client_secret;
            $this->logger->info('Obtenido client_secret de Stripe', [
                'client_secret' => $clientSecret,
            ]);
        } elseif (
            isset($stripeSubscription->latest_invoice) &&
            isset($stripeSubscription->latest_invoice->id)
        ) {
            $this->logger->warning('No se obtuvo client_secret directo, se intenta refrescar el invoice', [
                'invoice_id' => $stripeSubscription->latest_invoice->id,
            ]);
            try {
                $invoice = $this->stripe->invoices->retrieve(
                    $stripeSubscription->latest_invoice->id,
                    ['expand' => ['payment_intent']]
                );
                $this->logger->info('Respuesta de Stripe al refrescar invoice', [
                    'invoice' => (array) $invoice,
                ]);
                if (isset($invoice->payment_intent->client_secret)) {
                    $clientSecret = $invoice->payment_intent->client_secret;
                    $this->logger->info('Obtenido client_secret tras refrescar invoice', [
                        'client_secret' => $clientSecret,
                    ]);
                } else {
                    $this->logger->error('No se pudo obtener client_secret tras refrescar invoice', [
                        'invoice' => (array) $invoice,
                    ]);
                }
            } catch (\Throwable $e) {
                $this->logger->error('Error refrescando invoice para obtener client_secret', [
                    'exception_message' => $e->getMessage(),
                    'exception_trace' => $e->getTraceAsString(),
                    'invoice_id' => $stripeSubscription->latest_invoice->id,
                ]);
            }
        } else {
            $this->logger->error('Stripe: Falta el client_secret y no hay latest_invoice en la suscripción', [
                'stripeSubscription' => (array) $stripeSubscription,
            ]);
        }

        $this->logger->info('Resultado final de creación de suscripción', [
            'subscription_id' => $stripeSubscription->id,
            'client_secret' => $clientSecret,
        ]);

        return [
            'subscription_id' => $stripeSubscription->id,
            'client_secret' => $clientSecret,
        ];
    }

    public function getStripeLatestInvoiceClientSecret(string $stripeSubscriptionId): ?string
    {
        try {
            // 1. Obtén la suscripción de Stripe
            $stripeSubscription = $this->stripe->subscriptions->retrieve($stripeSubscriptionId, [
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // 2. Saca el client_secret del PaymentIntent (si existe)
            if (
                isset($stripeSubscription->latest_invoice) &&
                isset($stripeSubscription->latest_invoice->payment_intent) &&
                isset($stripeSubscription->latest_invoice->payment_intent->client_secret)
            ) {
                return $stripeSubscription->latest_invoice->payment_intent->client_secret;
            }

            // 3. Si no existe, intenta refrescar el invoice (opcional, depende del flujo)
            if (isset($stripeSubscription->latest_invoice->id)) {
                $invoice = $this->stripe->invoices->retrieve(
                    $stripeSubscription->latest_invoice->id,
                    ['expand' => ['payment_intent']]
                );
                if (isset($invoice->payment_intent) && isset($invoice->payment_intent->client_secret)) {
                    return $invoice->payment_intent->client_secret;
                }
            }
        } catch (\Throwable $e) {
            $this->logger->error('Error obteniendo client_secret de invoice Stripe', [
                'exception' => $e->getMessage(),
                'subscriptionId' => $stripeSubscriptionId,
            ]);
        }

        return null;
    }
}
