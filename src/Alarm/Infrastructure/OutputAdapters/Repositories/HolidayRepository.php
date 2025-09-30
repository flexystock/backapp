<?php

namespace App\Alarm\Infrastructure\OutputAdapters\Repositories;

use App\Alarm\Application\OutputPorts\Repositories\HolidayRepositoryInterface;
use App\Entity\Client\Holiday;
use Doctrine\ORM\EntityManagerInterface;

class HolidayRepository implements HolidayRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findByHolidayDate(\DateTimeInterface $holidayDate): ?Holiday
    {
        return $this->entityManager->getRepository(Holiday::class)->findOneBy([
            'holidayDate' => $holidayDate,
        ]);
    }

    public function save(Holiday $holiday): void
    {
        $this->entityManager->persist($holiday);
    }

    public function remove(Holiday $holiday): void
    {
        $this->entityManager->remove($holiday);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
