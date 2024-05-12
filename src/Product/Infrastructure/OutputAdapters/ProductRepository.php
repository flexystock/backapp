<?php
declare(strict_types=1);
namespace App\Product\Infrastructure\OutputAdapters;

use App\User\Domain\Entity\User;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

class ProductRepository extends ServiceEntityRepository
{

    public function findByName(string $name): ?Product
    {

        $name= $this->findOneBy(['name' => $name]);

        return $name;
    }

}