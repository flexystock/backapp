<?php

namespace App\Stripe\Infrastructure\InputAdapters;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Stripe\Application\DTO\SetupIntentRequest;
use App\Stripe\Application\InputPorts\SetupIntentUseCaseInterface;

class SetupIntentController extends AbstractController
{
    public function __construct(private SetupIntentUseCaseInterface $useCase)
    {
    }

    #[Route('/api/payment_method/setup_intent', name: 'create_setup_intent', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            return new JsonResponse(['message' => 'uuidClient requerido'], 400);
        }

        try {
            $response = $this->useCase->execute(new SetupIntentRequest($uuidClient));
            return new JsonResponse(['client_secret' => $response->clientSecret]);
        } catch (\Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }
}
