<?php

namespace App\User\Infrastructure\OutputAdapters;

use App\Entity\Main\PasswordReset;
use App\User\Application\OutputPorts\PasswordResetRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

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

    public function removeAllByEmail(string $email): void
    {
        $this->entityManager->createQueryBuilder()
            ->delete(PasswordReset::class, 'pr')
            ->where('pr.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->execute();
    }
}