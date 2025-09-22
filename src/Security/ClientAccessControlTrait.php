<?php

namespace App\Security;

use App\Entity\Main\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ClientAccessControlTrait
{
    /**
     * Verify client has active subscription or return error response.
     */
    protected function verifyClientAccess(Client $client): ?JsonResponse
    {
        if (!$this->isGranted('ACCESS_CLIENT', $client)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'CLIENT_SUBSCRIPTION_INACTIVE',
                'details' => 'El cliente no tiene una suscripción activa',
            ], Response::HTTP_PAYMENT_REQUIRED); // 402 Payment Required
        }

        return null; // null = acceso permitido
    }

    /**
     * Verify client access and throw exception if denied (for controllers that prefer exceptions).
     */
    protected function requireClientAccess(Client $client): void
    {
        $this->denyAccessUnlessGranted('ACCESS_CLIENT', $client);
    }
}
