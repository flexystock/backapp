<?php
namespace App\Alarm\Infrastructure\OutputAdapters\Repositories;

use App\Alarm\Application\OutputPorts\Repositories\HolidayRepositoryInterface;
use App\Entity\Client\Holiday;
use Doctrine\ORM\EntityManagerInterface;

class HolidayRepository implements HolidayRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function findByHolidayDate(\DateTimeInterface $holidayDate): ?Holiday
    {
        return $this->entityManager->getRepository(Holiday::class)->findOneBy([
            'holidayDate' => $holidayDate,
        ]);
    }

    /** @return array<int, Holiday> */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Holiday::class)
            ->findBy([], ['holidayDate' => 'ASC']);
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

    /**
     * Borra en lote por fechas exactas (DATE). Si pasas array vacío no hace nada.
     * @param array<int, \DateTimeInterface> $dates
     */
    public function deleteByDates(array $dates): void
    {
        if (empty($dates)) {
            return;
        }
        $qb = $this->entityManager->createQueryBuilder();
        $qb->delete(Holiday::class, 'h')
            ->where($qb->expr()->in('h.holidayDate', ':dates'))
            ->setParameter('dates', $dates)
            ->getQuery()
            ->execute();
    }
}
