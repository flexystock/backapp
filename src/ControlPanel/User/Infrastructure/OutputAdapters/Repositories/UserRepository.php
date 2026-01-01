<?php

declare(strict_types=1);

namespace App\ControlPanel\User\Infrastructure\OutputAdapters\Repositories;

use App\ControlPanel\User\Application\OutputPorts\UserRepositoryInterface;
use App\Entity\Main\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('u')
            ->getQuery()
            ->getResult();
    }

    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function findOneByUuid(?string $uuidUser): ?User
    {
        return $this->findOneBy(['uuid_user' => $uuidUser]);
    }
}
