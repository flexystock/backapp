<?php

declare(strict_types=1);

namespace App\Profile\Application\OutputPorts;

use App\Entity\Main\Profile;

interface ProfileRepositoryInterface
{
    public function findByName(string $name): ?Profile;

    public function save(Profile $profile): void;

    public function findAll(): array;
}
