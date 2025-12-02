<?php

namespace App\Report\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetAllReportsRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
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
