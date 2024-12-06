<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientAccessListener
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');

        // Lista de rutas a excluir
        $excludedRoutes = [
            'user_select_client',
            'api_login',
            'user_register',
            // Agrega otras rutas si es necesario
        ];

        if (in_array($routeName, $excludedRoutes, true)) {
            // No aplicar el listener en estas rutas
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (null === $token || !is_object($user = $token->getUser())) {
            // No hay usuario autenticado
            return;
        }

        if (!$user instanceof UserInterface) {
            // El usuario no es una instancia válida
            return;
        }

        $uuidClient = $user->getUuidClient();

        if (!$uuidClient) {
            throw new AccessDeniedHttpException('No client selected');
        }

        // Verificar que el usuario tiene acceso al cliente
        if (!$this->userHasAccessToClient($user, $uuidClient)) {
            throw new AccessDeniedHttpException('You do not have access to this client');
        }
    }

    private function userHasAccessToClient(UserInterface $user, string $uuidClient): bool
    {
        // Implementa la lógica para verificar si el usuario tiene acceso al cliente
        foreach ($user->getClients() as $client) {
            if ($client->getUuidClient() === $uuidClient) {
                return true;
            }
        }

        return false;
    }
}
