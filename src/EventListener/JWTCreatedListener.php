<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token || !is_object($user = $token->getUser())) {
            // No hay usuario autenticado
            return;
        }

        if (!$user instanceof UserInterface) {
            // El usuario no es una instancia válida
            return;
        }

        $payload = $event->getData();

        // Agregar el uuid_client al payload si está disponible
        if ($user->getUuidClient()) {
            $payload['uuid_client'] = $user->getUuidClient();
        }

        $event->setData($payload);
    }
}
