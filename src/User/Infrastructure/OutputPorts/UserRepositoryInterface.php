<?php
declare(strict_types=1);
namespace App\User\Infrastructure\OutputPorts;

use App\User\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

}