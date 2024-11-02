<?php

namespace App\User\Application\OutputPorts;

use App\Entity\Main\PasswordReset;

interface PasswordResetRepositoryInterface
{
    public function save(PasswordReset $passwordReset): void;
    public function findByEmail(string $email): ?PasswordReset;
    public function remove(PasswordReset $passwordReset): void;
}