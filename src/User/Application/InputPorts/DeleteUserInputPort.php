<?php

declare(strict_types=1);

namespace App\User\Application\InputPorts;

use App\User\Application\DTO\Management\DeleteUserRequest;

interface DeleteUserInputPort
{
    public function delete(DeleteUserRequest $request): void;
}
