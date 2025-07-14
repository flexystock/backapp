<?php

namespace App\Infrastructure\Services;

use App\Entity\Main\PaymentTransaction;
use App\Entity\Main\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentGatewayService
{
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function charge(Subscription $subscription, float $amount, string $currency = 'EUR', string $gateway = 'default'): PaymentTransaction
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
            $response = $this->httpClient->request('POST', 'https://example-payment-gateway/charge', [
                'json' => [
                    'amount' => $amount,
                    'currency' => $currency,
                    'reference' => $transaction->getId(),
                ],
            ]);

            $data = $response->toArray(false);
            $transaction->setTransactionReference($data['reference'] ?? null);

            if (($data['status'] ?? 'failed') === 'success') {
                $transaction->setStatus('paid');
                $subscription->setPaymentStatus('paid');
            } else {
                $transaction->setStatus('failed');
                $subscription->setPaymentStatus('failed');
            }
        } catch (\Throwable $e) {
            $transaction->setStatus('failed');
            $subscription->setPaymentStatus('failed');
            $this->logger->error('Payment gateway error', ['exception' => $e]);
        }

        $this->entityManager->flush();

        return $transaction;
    }
}
