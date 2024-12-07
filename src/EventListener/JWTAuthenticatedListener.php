<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTAuthenticatedListener
{
    private Security $security;
    private LoggerInterface $logger;

    public function __construct(Security $security, LoggerInterface $logger)
    {
        $this->security = $security;
        $this->logger = $logger;
    }

    public function onJWTAuthenticated(JWTAuthenticatedEvent $event): void
    {
        $payload = $event->getPayload(); // Accede al payload

        // Log para verificar el payload recibido
        $this->logger->info('JWTAuthenticatedListener: Payload recibido.', $payload);

        if (isset($payload['uuid_client'])) {
            // Obtener el token desde el evento
            $token = $event->getToken();

            // Obtener el usuario desde el token
            $user = $token->getUser();

            if (!$user instanceof UserInterface) {
                $this->logger->warning('JWTAuthenticatedListener: Usuario no es una instancia válida.');
                return;
            }

            // Asignar uuid_client al usuario
            $user->setSelectedClientUuid($payload['uuid_client']);

            // Log para verificar la asignación
            $this->logger->info('JWTAuthenticatedListener: uuid_client asignado a usuario.', ['uuid_client' => $user->getSelectedClientUuid()]);
        } else {
            $this->logger->warning('JWTAuthenticatedListener: uuid_client no encontrado en el payload del JWT.');
        }
    }
}
