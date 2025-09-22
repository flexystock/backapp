<?php

namespace App\Client\Application\UseCases;

use App\Client\Application\InputPorts\GetInfoClientInputPort;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Main\Client;

class GetInfoClientUseCase implements GetInfoClientInputPort
{
    private ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getInfo(string $uuidClient): Client
    {
        return $this->clientRepository->findByUuid($uuidClient);
    }
}
