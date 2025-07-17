<?php

namespace App\Client\Application\DTO;

use App\Entity\Main\Client;
// use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Annotation\Groups;

class ClientDTO
{
    #[Groups(['client'])]
    public string $uuid;
    #[Groups(['client'])]
    public string $name;
    #[Groups(['client'])]
    public bool $hasActiveSubscription;
    // Agrega otros campos necesarios

    public function __construct(Client $client, bool $hasActiveSubscription = false)
    {
        $this->uuid = $client->getUuidClient();
        $this->name = $client->getName();
        $this->hasActiveSubscription = $hasActiveSubscription;
    }
}
