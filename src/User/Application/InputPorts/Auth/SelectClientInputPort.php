<?php

declare(strict_types=1);

namespace App\User\Application\InputPorts\Auth;

use Symfony\Component\Security\Core\User\UserInterface;

interface SelectClientInputPort
{
    /**
     * Selecciona un cliente y genera un token JWT que incluye el uuid_client.
     *
     * @param UserInterface $user       el usuario autenticado
     * @param string        $uuidClient el UUID del cliente a seleccionar
     *
     * @return string el token JWT generado
     *
     * @throws AccessDeniedException si el usuario no tiene acceso al cliente
     */
    public function selectClient(UserInterface $user, string $uuidClient): string;
}
