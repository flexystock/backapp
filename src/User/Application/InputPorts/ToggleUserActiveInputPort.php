<?php

declare(strict_types=1);

namespace App\User\Application\InputPorts;

use App\User\Application\DTO\Management\ToggleUserActiveRequest;

interface ToggleUserActiveInputPort
{
    public function toggle(ToggleUserActiveRequest $request): bool;
}
