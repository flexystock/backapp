<?php

namespace App\Infrastructure\Services;

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

    public function charge(Subscription $subscription, float $amount, string $currency = 'EUR', string $gateway = 'stripe'): PaymentTransaction
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

        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount' => (int) round($amount * 100),
                'currency' => strtolower($currency),
                'metadata' => [
                    'transaction_id' => $transaction->getId(),
                    'subscription_uuid' => $subscription->getUuidSubscription(),
                ],
            ]);

            $transaction->setTransactionReference($intent->id);

            if ($intent->status === 'succeeded') {
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

        return $transaction;
    }
}
