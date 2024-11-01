<?php

namespace App\User\Infrastructure\OutputAdapters;

use App\Entity\Main\PasswordReset;
use App\User\Infrastructure\OutputPorts\PasswordResetRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function save(PasswordReset $passwordReset): void
    {
        $this->entityManager->persist($passwordReset);
        $this->entityManager->flush();
    }

    public function findByEmail(string $email): ?PasswordReset
    {
        return $this->entityManager->getRepository(PasswordReset::class)->findOneBy(['email' => $email]);
    }

    public function remove(PasswordReset $passwordReset): void
    {
        $this->entityManager->remove($passwordReset);
        $this->entityManager->flush();
    }
}