<?php

namespace App\Ttn\Application\Services;

use App\Entity\Client\BusinessHour;
use App\Entity\Client\Holiday;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;

class BusinessHoursCheckerService
{
    public function isHoliday(EntityManagerInterface $entityManager, \DateTimeImmutable $dateTime): bool
    {
        $holidayRepo = $entityManager->getRepository(Holiday::class);

        $count = $holidayRepo->createQueryBuilder('h')
            ->select('COUNT(h.id)')
            ->where('h.holidayDate = :date')
            ->setParameter('date', $dateTime->setTime(0, 0), Types::DATE_IMMUTABLE)
            ->getQuery()
            ->getSingleScalarResult();

        return ((int) $count) > 0;
    }

    public function isWithinBusinessHours(EntityManagerInterface $entityManager, \DateTimeImmutable $dateTime): bool
    {
        $dayOfWeek = (int) $dateTime->format('N');
        $businessHours = $entityManager->getRepository(BusinessHour::class)->findBy([
            'dayOfWeek' => $dayOfWeek,
        ]);

        foreach ($businessHours as $businessHour) {
            if ($businessHour->coversDateTime($dateTime)) {
                return true;
            }
        }

        return false;
    }
}
