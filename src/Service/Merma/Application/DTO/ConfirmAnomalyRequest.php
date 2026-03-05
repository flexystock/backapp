<?php

namespace App\Service\Merma\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ConfirmAnomalyRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_EVENT_ID')]
    #[Assert\Positive(message: 'INVALID_EVENT_ID')]
    private int $eventId;

    public function __construct(string $uuidClient, int $eventId)
    {
        $this->uuidClient = $uuidClient;
        $this->eventId    = $eventId;
    }

    public function getUuidClient(): string { return $this->uuidClient; }
    public function getEventId(): int       { return $this->eventId; }
}
