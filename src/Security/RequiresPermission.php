<?php

namespace App\Security;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
class RequiresPermission
{
    public function __construct(
        public readonly string | array $permissions,
        public readonly string $message = 'No tienes permisos para realizar esta acción'
    ) {
    }

    public function getPermissions(): array
    {
        return is_string($this->permissions) ? [$this->permissions] : $this->permissions;
    }
}
