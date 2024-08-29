<?php

namespace App\Client\Application;
use App\Client\Infrastructure\InputPorts\GetAllClientsInputPort;
use App\Client\Infrastructure\OutputPorts\ClientRepositoryInterface;

class GetAllClientsUseCase implements GetAllClientsInputPort
{
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository){
        $this->clientRepository = $clientRepository;
    }

    public function getAll(): ?array
    {
        return $this->clientRepository->findAll();
    }

}