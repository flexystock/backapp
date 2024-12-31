<?php

declare(strict_types=1);

namespace App\User\Application\UseCases\Auth;

use App\User\Application\InputPorts\Auth\SelectClientInputPort;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class SelectClientUseCase implements SelectClientInputPort
{
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
    ) {
        $this->jwtManager = $jwtManager;
    }

    public function selectClient(UserInterface $user, string $uuidClient): string
    {
        if (!$this->userHasAccessToClient($user, $uuidClient)) {
            throw new AccessDeniedException('YOU_DO_NOT_HAVE_ACCESS_TO_THIS_CLIENT');
        }

        // Establecer el uuid_client en el usuario
        $user->setUuidClient($uuidClient);

        // Generar el JWT con el uuid_client
        $token = $this->jwtManager->create($user);

        return $token;
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
