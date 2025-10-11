<?php

namespace App\Alarm\Infrastructure\OutputAdapters\Repositories;

use App\Alarm\Application\OutputPorts\Repositories\BusinessHourRepositoryInterface;
use App\Entity\Client\BusinessHour;
use Doctrine\ORM\EntityManagerInterface;

class BusinessHourRepository implements BusinessHourRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findByDayOfWeek(int $dayOfWeek): ?BusinessHour
    {
        return $this->entityManager->getRepository(BusinessHour::class)->findOneBy([
            'dayOfWeek' => $dayOfWeek,
        ]);
    }

    /**
     * @return array<int, BusinessHour>
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(BusinessHour::class)->findBy([], ['dayOfWeek' => 'ASC']);
    }

    public function save(BusinessHour $businessHour): void
    {
        $this->entityManager->persist($businessHour);
    }

    public function remove(BusinessHour $businessHour): void
    {
        $this->entityManager->remove($businessHour);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
