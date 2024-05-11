<?php
declare(strict_types=1);
namespace App\Client\Infrastructure\OutputAdapters;

use App\Client\Domain\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    // Métodos adicionales como encontrar por nombre, etc.
}