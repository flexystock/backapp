<?php

namespace App\Stripe\Infrastructure\InputAdapters;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Stripe\Application\DTO\PaymentMethodRequest;
use App\Stripe\Application\InputPorts\PaymentMethodUseCaseInterface;


class PaymentMethodController extends AbstractController
{
    public function __construct(private PaymentMethodUseCaseInterface $useCase)
    {
    }

    #[Route('/api/payment_method/default', name: 'get_default_payment_method', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            return new JsonResponse(['message' => 'uuidClient required'], 400);
        }

        $response = $this->useCase->execute(new PaymentMethodRequest($uuidClient));

        if (!$response->paymentMethodId) {
            return new JsonResponse(['message' => 'No payment method found'], 404);
        }

        return new JsonResponse(['paymentMethodId' => $response->paymentMethodId]);
    }
}
