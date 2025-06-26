<?php

namespace App\User\Application\UseCases;

use App\Client\Application\DTO\ClientDTO;
use App\Client\Application\DTO\ClientDTOCollection;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Main\User;
use App\User\Application\InputPorts\GetUserClientsInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

class GetUserClientsUseCase implements GetUserClientsInterface
{
    private UserRepositoryInterface $userRepository;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        ClientRepositoryInterface $clientRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
    }

    public function getUserClients(string $userId): ClientDTOCollection
    {
        // Obtener el usuario por ID
        $user = $this->userRepository->findByUuid($userId);

        if (!$user) {
            throw new \Exception('USER_NOT_FOUND');
        }

        // Si el usuario es root, obtiene todos los clientes
        if (in_array('ROLE_ROOT', $user->getRoles())) {
            $clients = $this->clientRepository->findAll();
        } else {
            // Obtener los clientes asociados al usuario
            $clients = $user->getClients(); // Asumiendo que tienes una relación en la entidad User
        }

        // Convertir los clientes a DTOs
        $clientDTOs = new ClientDTOCollection();

        foreach ($clients as $client) {
            $clientDTOs->add(new ClientDTO($client));
        }

        return $clientDTOs;
    }
}
