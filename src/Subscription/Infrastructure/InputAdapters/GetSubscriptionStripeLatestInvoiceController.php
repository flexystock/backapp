<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Subscription\Application\DTO\GetSubscriptionStripeLatestInvoiceRequest;
use App\Subscription\Application\InputPorts\GetSubscriptionStripeLatestInvoiceUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GetSubscriptionStripeLatestInvoiceController extends AbstractController
{
    private GetSubscriptionStripeLatestInvoiceUseCaseInterface $useCase;

    public function __construct(GetSubscriptionStripeLatestInvoiceUseCaseInterface $useCase)
    {
        $this->useCase = $useCase;
    }

    #[Route('/api/subscription/stripe_latest_invoice', name: 'api_subscription_stripe_latest_invoice', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Valida que recibes el uuidClient
        if (empty($data['uuid'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'uuidClient es requerido'
            ], 400);
        }

        $response = $this->useCase->execute(new GetSubscriptionStripeLatestInvoiceRequest($data['uuid']));
        if ($response->getError()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $response->getError(),
            ], 422);
        }

        return new JsonResponse([
            'status' => 'success',
            'client_secret' => $response->getClientSecret(),
        ]);
    }
}
