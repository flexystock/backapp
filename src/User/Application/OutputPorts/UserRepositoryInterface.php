<?php
declare(strict_types=1);
namespace App\User\Application\OutputPorts;

use App\Entity\Main\User;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;

    public function save(User $user): void;

    public function findAll(): array;

    public function findOneByVerificationToken(string $token): ?User;

}