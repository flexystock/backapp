<?php

namespace App\Client\Application\DTO;

use App\Entity\Main\Client;
//use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Annotation\Groups;

class ClientDTO
{
    #[Groups(['client'])]
    public string $uuid;
    #[Groups(['client'])]
    public string $name;
    // Agrega otros campos necesarios

    public function __construct(Client $client)
    {
        $this->uuid = $client->getUuidClient();
        $this->name = $client->getName();
    }
}