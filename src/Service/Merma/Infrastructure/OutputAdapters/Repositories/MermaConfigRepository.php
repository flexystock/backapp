<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\MermaConfig;
use App\Entity\Client\MermaMonthlyReport;
use App\Entity\Client\ScaleEvent;
use App\Service\Merma\Application\OutputPorts\MermaConfigRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\MermaMonthlyReportRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleReadingRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

// ═══════════════════════════════════════════════════════
// MermaConfigRepository
// ═══════════════════════════════════════════════════════

final class MermaConfigRepository extends ServiceEntityRepository implements MermaConfigRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct($registry, MermaConfig::class);
    }

    public function save(MermaConfig $config): void
    {
        $this->em->persist($config);
        $this->em->flush();
    }

    public function findByProductId(int $productId): ?MermaConfig
    {
        return $this->findOneBy(['productId' => $productId]);
    }

    public function createDefaultForProduct(int $productId): MermaConfig
    {
        $config = new MermaConfig();
        $config->setProductId($productId);
        // Defaults hostelería: 80% rendimiento, servicio 09:00-24:00, umbral 200g
        $this->save($config);
        return $config;
    }
}
