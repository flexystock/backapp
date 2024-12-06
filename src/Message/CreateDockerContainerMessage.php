<?php

namespace App\Message;

class CreateDockerContainerMessage
{
    private string $clientId;

    public function __construct(string $clientId)
    {
        $this->clientId = $clientId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }
}
