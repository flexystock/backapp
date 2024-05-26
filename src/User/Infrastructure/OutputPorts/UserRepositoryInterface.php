<?php
declare(strict_types=1);
namespace App\User\Infrastructure\OutputPorts;

use App\Entity\Main\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function save(User $user): void;

}