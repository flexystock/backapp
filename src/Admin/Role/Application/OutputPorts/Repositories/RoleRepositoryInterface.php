<?php

namespace App\Admin\Role\Application\OutputPorts\Repositories;

use App\Entity\Main\Role;

interface RoleRepositoryInterface
{
    public function findByName(string $name): ?Role;

    public function save(Role $role): void;
}
