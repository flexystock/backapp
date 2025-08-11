<?php

namespace App\Infrastructure\Services;

use App\Entity\Main\PaymentTransaction;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeWebhookController
{
    private string $stripeWebhookSecret;

    public function __construct(string $stripeWebhookSecret)
    {
        $this->stripeWebhookSecret = $stripeWebhookSecret;
    }

    #[Route('/api/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function __invoke(Request $request, EntityManagerInterface $em, LoggerInterface $logger): Response
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

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                /** @var PaymentTransaction|null $transaction */
                $transaction = $em->getRepository(PaymentTransaction::class)
                    ->findOneBy(['transactionReference' => $paymentIntent->id]);
                if ($transaction && $transaction->getSubscription()) {
                    $transaction->setStatus('paid');
                    $transaction->getSubscription()->setPaymentStatus('paid');
                    $em->flush();
                    $logger->info('Payment marked as paid', ['id' => $transaction->getId()]);
                }
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $transaction = $em->getRepository(PaymentTransaction::class)
                    ->findOneBy(['transactionReference' => $paymentIntent->id]);
                if ($transaction && $transaction->getSubscription()) {
                    $transaction->setStatus('failed');
                    $transaction->getSubscription()->setPaymentStatus('failed');
                    $em->flush();
                    $logger->info('Payment marked as failed', ['id' => $transaction->getId()]);
                }
                break;

            // Otros eventos Stripe aquí si los necesitas...
        }

        return new Response('OK', Response::HTTP_OK);
    }
}
