<?php
namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\SyncAlarmHolidaysRequest;
use App\Alarm\Application\DTO\SyncAlarmHolidaysResponse;
use App\Alarm\Application\InputPorts\SyncAlarmHolidaysUseCaseInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\HolidayRepository;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\ClientConfigRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Holiday;
use App\Entity\Client\HolidayLog;
use App\Entity\Client\ClientConfig;
use App\Infrastructure\Services\ClientConnectionManager;
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
        $clientConfigRepository = new ClientConfigRepository($em);

        $previousHolidays = $this->formatHolidays($repo->findAll());

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
                'name' => $h['name'] ?? 'Dia festivo',
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
        $final = $this->formatHolidays($repo->findAll());

        if ($created !== [] || $updated !== [] || $deleted !== []) {
            $log = (new HolidayLog())
                ->setUuidClient($client->getUuidClient())
                ->setUuidUserModification($request->uuidUser)
                ->setDateModification($now)
                ->setDataBeforeModification($this->encodeForLog($previousHolidays))
                ->setDataAfterModification($this->encodeForLog($final));

            $em->persist($log);
            $em->flush();
        }

        $this->updateClientConfig(
            $clientConfigRepository,
            $request->isCheckHolidaysEnabled(),
            $request->uuidUser,
            $now
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

    /**
     * @param array<int, Holiday> $holidays
     * @return array<int, array{id: ?int, holiday_date: string, name: ?string}>
     */
    private function formatHolidays(array $holidays): array
    {
        $data = array_map(
            static fn (Holiday $h): array => [
                'id'           => $h->getId(),
                'holiday_date' => $h->getHolidayDate()->format('Y-m-d'),
                'name'         => $h->getName(),
            ],
            $holidays
        );

        usort(
            $data,
            static fn (array $a, array $b): int => $a['holiday_date'] <=> $b['holiday_date']
        );

        return $data;
    }

    /**
     * @param array<int, array<string, mixed>> $data
     */
    private function encodeForLog(array $data): string
    {
        $encoded = json_encode($data, JSON_UNESCAPED_UNICODE);

        if (false === $encoded) {
            $this->logger->error('FAILED_TO_ENCODE_HOLIDAY_LOG_DATA', [
                'error' => json_last_error_msg(),
            ]);

            throw new \RuntimeException('FAILED_TO_ENCODE_HOLIDAY_LOG_DATA');
        }

        return $encoded;
    }

    private function updateClientConfig(
        ClientConfigRepository $clientConfigRepository,
        bool $isEnabled,
        string $uuidUser,
        \DateTimeInterface $timestamp
    ): void {
        $clientConfig = $clientConfigRepository->findConfig();

        if (!$clientConfig) {
            $clientConfig = (new ClientConfig())
                ->setUuidUserCreation($uuidUser)
                ->setDatehourCreation($timestamp);
        } else {
            $clientConfig
                ->setUuidUserModification($uuidUser)
                ->setDatehourModification($timestamp);
        }

        $clientConfig->setCheckHolidays($isEnabled);

        $clientConfigRepository->save($clientConfig);
        $clientConfigRepository->flush();
    }
}
