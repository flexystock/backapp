<?php

namespace App\IA\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePredictionConsumeAllProductRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_UUID_CLIENT')]
    #[Assert\Uuid(message: 'INVALID_UUID_CLIENT')]
    private string $uuidClient;

    public function __construct(string $uuidClient)
    {
        $this->uuidClient = $uuidClient;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }
}
