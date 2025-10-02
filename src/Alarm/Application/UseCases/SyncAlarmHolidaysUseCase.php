<?php
namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\SyncAlarmHolidaysRequest;
use App\Alarm\Application\DTO\SyncAlarmHolidaysResponse;
use App\Alarm\Application\InputPorts\SyncAlarmHolidaysUseCaseInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\HolidayRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Holiday;
use App\Infrastructure\Services\ClientConnectionManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class SyncAlarmHolidaysUseCase implements SyncAlarmHolidaysUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {}

    public function execute(SyncAlarmHolidaysRequest $request): SyncAlarmHolidaysResponse
    {
        $client = $this->clientRepository->findByUuid($request->uuidClient);
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em = $this->connectionManager->getEntityManager($client->getUuidClient());
        $repo = new HolidayRepository($em);

        // Normaliza fechas
        $newDates = [];
        $byDate   = [];
        foreach ($request->holidays as $h) {
            if (!isset($h['date'])) {
                throw new \RuntimeException('INVALID_HOLIDAY_DATE');
            }
            $date = $this->normalizeDate($h['date']);
            $key  = $date->format('Y-m-d');
            $newDates[] = $key;
            $byDate[$key] = [
                'date' => $date,
                'name' => $h['name'] ?? null,
            ];
        }
        $newDates = array_values(array_unique($newDates));
        $now = new \DateTimeImmutable();

        $created = [];
        $updated = [];
        $deleted = [];

        $em->wrapInTransaction(function () use ($repo, $em, $newDates, $byDate, $request, $now, &$created, &$updated, &$deleted) {
            // 1) existentes
            $existing = $repo->findAll();
            $existingMap = []; // 'Y-m-d' => Holiday
            foreach ($existing as $e) {
                $existingMap[$e->getHolidayDate()->format('Y-m-d')] = $e;
            }

            // 2) borrar los que ya no vienen (uno a uno, para evitar IN con DateTimes)
            $toDeleteKeys = array_diff(array_keys($existingMap), $newDates);
            foreach ($toDeleteKeys as $dateStr) {
                $em->remove($existingMap[$dateStr]);
                $deleted[] = ['holiday_date' => $dateStr];
            }

            // 3) insertar los nuevos
            $toInsert = array_diff($newDates, array_keys($existingMap));
            foreach ($toInsert as $dateStr) {
                $h = new Holiday();
                $h->setHolidayDate($byDate[$dateStr]['date']);
                $h->setName($byDate[$dateStr]['name']);
                $h->setUuidUserCreation($request->uuidUser);
                $h->setDatehourCreation($now);
                $em->persist($h);

                $created[] = [
                    'holiday_date' => $dateStr,
                    'name' => $byDate[$dateStr]['name'],
                ];
            }

            // 4) actualizar nombres si han cambiado
            $toMaybeUpdate = array_intersect($newDates, array_keys($existingMap));
            foreach ($toMaybeUpdate as $dateStr) {
                $e = $existingMap[$dateStr];
                $newName = $byDate[$dateStr]['name'];
                if ($e->getName() !== $newName) {
                    $e->setName($newName);
                    $e->setUuidUserModification($request->uuidUser);
                    $e->setDatehourModification($now);
                    $updated[] = [
                        'holiday_date' => $dateStr,
                        'name' => $newName,
                    ];
                }
            }
        });

        // lista final ordenada
        $final = array_map(
            fn(Holiday $h) => [
                'id'           => $h->getId(),
                'holiday_date' => $h->getHolidayDate()->format('Y-m-d'),
                'name'         => $h->getName(),
            ],
            $repo->findAll()
        );

        // devolvemos el listado final + diffs (útil para depurar)
        return new SyncAlarmHolidaysResponse(
            holidays: $final,
            created:  $created,
            updated:  $updated,
            deleted:  $deleted,
        );
    }

    private function normalizeDate(string $input): \DateTimeImmutable
    {
        $d = \DateTimeImmutable::createFromFormat('Y-m-d', $input);
        if ($d !== false) {
            return $d->setTime(0,0);
        }
        try {
            return (new \DateTimeImmutable($input))->setTime(0,0);
        } catch (\Throwable $t) {
            $this->logger->warning('INVALID_HOLIDAY_DATE', ['value' => $input]);
            throw new \RuntimeException('INVALID_HOLIDAY_DATE');
        }
    }
}
