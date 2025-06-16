<?php

namespace App\Admin\Role\Infrastructure\OutputAdapters\Repositories;

use App\Admin\Role\Application\OutputPorts\Repositories\RoleRepositoryInterface;
use App\Entity\Main\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoleRepository extends ServiceEntityRepository implements RoleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }

    public function findByName(string $name): ?Role
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function save(Role $role): void
    {
        $em = $this->getEntityManager();
        $em->persist($role);
        $em->flush();
    }
}
