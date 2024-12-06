<?php

namespace App\Client\Application\UseCases;

use App\Client\Application\InputPorts\GetAllClientsInputPort;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;

class GetAllClientsUseCase implements GetAllClientsInputPort
{
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getAll(): ?array
    {
        return $this->clientRepository->findAll();
    }
}
