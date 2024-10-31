<?php

namespace App\Client\Application;
use App\Client\Infrastructure\InputPorts\GetClientByUuidInputPort;
use App\Client\Infrastructure\OutputPorts\ClientRepositoryInterface;
use App\Entity\Main\Client;

class GetUuidClientUseCase implements GetClientByUuidInputPort
{
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository){
        $this->clientRepository = $clientRepository;
    }

    public function getByUuid(string $uuid): ?Client
    {
        return $this->clientRepository->findByUuid($uuid);
    }

}