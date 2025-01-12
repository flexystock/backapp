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
        // 1) Obtenemos el usuario directamente del evento
        $user = $event->getUser();
        if (!$user instanceof UserInterface) {
            // Por si no fuera una instancia válida, salimos
            return;
        }

        // 2) Obtenemos el payload
        $payload = $event->getData();

        // 3) Añadimos la info que quieras en el JWT
        //    dataUser, uuid_client, etc.
        $payload['dataUser'] = [
            'mail' => $user->getEmail(),
            'fullname' => $user->getName(),
            'surname' => $user->getSurnames(),
        ];

        // (Opcional) Eliminar 'username'
        // unset($payload['username']);

        $event->setData($payload);
    }
}
