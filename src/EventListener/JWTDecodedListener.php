<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTDecodedListener
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onJWTDecoded(JWTDecodedEvent $event)
    {
        $payload = $event->getPayload();

        if (isset($payload['uuid_client'])) {
            $token = $this->tokenStorage->getToken();

            if (null === $token || !is_object($user = $token->getUser())) {
                // No hay usuario autenticado
                return;
            }

            if (!$user instanceof UserInterface) {
                // El usuario no es una instancia válida
                return;
            }

            $user->setUuidClient($payload['uuid_client']);
        }
    }
}
