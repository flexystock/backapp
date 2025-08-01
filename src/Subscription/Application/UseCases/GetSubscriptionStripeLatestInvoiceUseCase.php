<?php

namespace App\Subscription\Application\UseCases;

use App\Subscription\Application\DTO\GetSubscriptionStripeLatestInvoiceRequest;
use App\Subscription\Application\DTO\GetSubscriptionStripeLatestInvoiceResponse;
use App\Subscription\Application\InputPorts\GetSubscriptionStripeLatestInvoiceUseCaseInterface;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use App\Infrastructure\Services\PaymentGatewayService;
use Psr\Log\LoggerInterface;


class GetSubscriptionStripeLatestInvoiceUseCase implements GetSubscriptionStripeLatestInvoiceUseCaseInterface
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private PaymentGatewayService $paymentGateway;
    private LoggerInterface $logger;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        PaymentGatewayService $paymentGateway,
        LoggerInterface $logger
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->paymentGateway = $paymentGateway;
        $this->logger = $logger;
    }

    public function execute(GetSubscriptionStripeLatestInvoiceRequest $request): GetSubscriptionStripeLatestInvoiceResponse
    {
        $subscription = $this->subscriptionRepository->findOneByUuid($request->getSubscriptionUuid());
        if (!$subscription || !$subscription->getStripeSubscriptionId()) {
            $this->logger->error('Suscripción no encontrada o sin Stripe ID', [
                'uuid' => $request->getSubscriptionUuid()
            ]);
            return new GetSubscriptionStripeLatestInvoiceResponse(null, 'SUBSCRIPTION_NOT_FOUND');
        }

        $clientSecret = $this->paymentGateway->getStripeLatestInvoiceClientSecret($subscription->getStripeSubscriptionId());

        if (!$clientSecret) {
            $this->logger->error('No se pudo obtener client_secret de Stripe', [
                'stripeSubscriptionId' => $subscription->getStripeSubscriptionId(),
            ]);
            return new GetSubscriptionStripeLatestInvoiceResponse(null, 'CLIENT_SECRET_NOT_FOUND');
        }

        return new GetSubscriptionStripeLatestInvoiceResponse($clientSecret);
    }
}
