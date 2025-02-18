<?php
namespace App\Alarm\Infrastructure\OutputAdapters\Repositories;

use App\Alarm\Application\OutputPorts\Repositories\AlarmRepositoryInterface;
use App\Entity\Client\AlarmConfig;
use Doctrine\ORM\EntityManagerInterface;

class AlarmConfigRepository implements AlarmRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findByUuid(string $uuid): ?AlarmConfig
    {
        return $this->em->getRepository(AlarmConfig::class)->findOneBy(['uuid' => $uuid]);
    }

    public function save(AlarmConfig $alarm): void
    {
        $this->em->persist($alarm);
        $this->em->flush();
    }

    public function remove(AlarmConfig $alarm): void
    {
        $this->em->remove($alarm);
        $this->em->flush();
    }
}
