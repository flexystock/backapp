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
    private ClientRepositoryInterface $clientRepository;
    private Security $security;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        ClientRepositoryInterface $clientRepository,
        Security $security,
    ) {
        $this->jwtManager = $jwtManager;
        $this->clientRepository = $clientRepository;
        $this->security = $security;
    }

    public function execute(string $uuidClient): string
    {
        $client = $this->clientRepository->findByUuid($uuidClient);

        if (!$client) {
            throw new AccessDeniedException('Client not found.');
        }

        // Verificar que el usuario tiene acceso a este cliente
        $user = $this->security->getUser();
        if (!$user || !$user->getClients()->contains($client)) {
            throw new AccessDeniedException('You do not have access to this client.');
        }

        // Genera un nuevo JWT que incluya 'uuid_client'
        $payload = [
            'uuid_client' => $uuidClient,
            // otros campos si es necesario
        ];

        // Genera el nuevo token
        $newToken = $this->jwtManager->create($user, $payload);

        return $newToken;
    }

    public function selectClient(UserInterface $user, string $uuidClient): string
    {
        if (!$this->userHasAccessToClient($user, $uuidClient)) {
            throw new AccessDeniedException('You do not have access to this client');
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
