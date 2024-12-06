<?php

namespace App\Client\Application\UseCases;

use App\Client\Application\InputPorts\GetClientByNameInputPort;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Main\Client;

class GetNameClientUseCase implements GetClientByNameInputPort
{
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getByName(string $name): ?Client
    {
        return $this->clientRepository->findByName($name);
    }
}
