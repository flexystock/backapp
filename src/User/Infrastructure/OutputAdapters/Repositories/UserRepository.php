<?php

declare(strict_types=1);

namespace App\User\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Main\User;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, User::class);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findOneByVerificationToken(string $token): ?User
    {
        return $this->findOneBy(['verification_token' => $token]);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function save(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function findByUuid(string $uuid): ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.clients', 'c')
            ->addSelect('c')
            ->where('u.uuid_user = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByClientUuid(string $uuidClient): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.clients', 'c')
            ->where('c.uuid_client = :uuidClient')
            ->setParameter('uuidClient', $uuidClient)
            ->getQuery()
            ->getResult();
    }

    public function findOneByUuid(string $uuidUser): ?User
    {
        return $this->findOneBy(['uuid_user' => $uuidUser]);
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
