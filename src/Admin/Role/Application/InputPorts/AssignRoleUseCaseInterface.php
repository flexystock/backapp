<?php

namespace App\Admin\Role\Application\InputPorts;

use App\Admin\Role\Application\DTO\AssignRoleRequest;
use App\Admin\Role\Application\DTO\AssignRoleResponse;

interface AssignRoleUseCaseInterface
{
    public function execute(AssignRoleRequest $request): AssignRoleResponse;
}
