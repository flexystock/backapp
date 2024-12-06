<?php

declare(strict_types=1);

namespace App\Profile\Infrastructure\OutputAdapters;

use App\Entity\Main\Profile;
use App\Profile\Infrastructure\OutputPorts\ProfileRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ProfileRepository extends ServiceEntityRepository implements ProfileRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, Profile::class);
    }

    public function findByName(string $name): ?Profile
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function save(Profile $profile): void
    {
        $this->entityManager->persist($profile);
        $this->entityManager->flush();
    }
}
