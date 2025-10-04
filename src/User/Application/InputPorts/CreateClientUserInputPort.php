<?php

declare(strict_types=1);

namespace App\User\Application\InputPorts;

use App\Entity\Main\User;
use App\User\Application\DTO\Management\CreateClientUserRequest;

interface CreateClientUserInputPort
{
    public function create(CreateClientUserRequest $request): User;
}
