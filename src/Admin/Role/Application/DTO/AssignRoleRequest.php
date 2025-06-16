<?php

namespace App\Admin\Role\Application\DTO;

class AssignRoleRequest
{
    private string $userUuid;
    private string $roleName;

    public function __construct(string $userUuid, string $roleName)
    {
        $this->userUuid = $userUuid;
        $this->roleName = $roleName;
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getRoleName(): string
    {
        return $this->roleName;
    }
}
