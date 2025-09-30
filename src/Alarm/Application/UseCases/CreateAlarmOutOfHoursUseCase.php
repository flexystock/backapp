<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\CreateAlarmOutOfHoursRequest;
use App\Alarm\Application\DTO\CreateAlarmOutOfHoursResponse;
use App\Alarm\Application\InputPorts\CreateAlarmOutOfHoursUseCaseInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\BusinessHourRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\BusinessHour;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class CreateAlarmOutOfHoursUseCase implements CreateAlarmOutOfHoursUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(CreateAlarmOutOfHoursRequest $request): CreateAlarmOutOfHoursResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $uuidClient = $client->getUuidClient();
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);
        $businessHourRepository = new BusinessHourRepository($entityManager);

        $normalizedBusinessHours = $this->normalizeBusinessHours($request->getBusinessHours());

        $hasChanges = false;
        foreach ($normalizedBusinessHours as $dayData) {
            $existingBusinessHour = $businessHourRepository->findByDayOfWeek($dayData['day_of_week']);

            if (null === $dayData['start_time'] && null === $dayData['end_time']) {
                if ($existingBusinessHour) {
                    $businessHourRepository->remove($existingBusinessHour);
                    $hasChanges = true;
                }

                continue;
            }

            if (!$existingBusinessHour) {
                $existingBusinessHour = new BusinessHour();
                $existingBusinessHour->setDayOfWeek($dayData['day_of_week']);
                $existingBusinessHour->setUuidUserCreation($request->getUuidUser() ?? 'system');
                $existingBusinessHour->setDatehourCreation($request->getTimestamp() ?? new \DateTimeImmutable());
            } else {
                $existingBusinessHour->setUuidUserModification($request->getUuidUser());
                $existingBusinessHour->setDatehourModification($request->getTimestamp() ?? new \DateTimeImmutable());
            }

            $existingBusinessHour->setStartTime($dayData['start_time']);
            $existingBusinessHour->setEndTime($dayData['end_time']);
            $existingBusinessHour->setStartTime2($dayData['start_time2']);
            $existingBusinessHour->setEndTime2($dayData['end_time2']);

            $businessHourRepository->save($existingBusinessHour);
            $hasChanges = true;
        }

        if ($hasChanges) {
            $businessHourRepository->flush();
        }

        $businessHours = array_map(
            static function (BusinessHour $businessHour): array {
                return [
                    'day_of_week' => $businessHour->getDayOfWeek(),
                    'start_time' => $businessHour->getStartTime()->format('H:i:s'),
                    'end_time' => $businessHour->getEndTime()->format('H:i:s'),
                    'start_time2' => $businessHour->getStartTime2()?->format('H:i:s'),
                    'end_time2' => $businessHour->getEndTime2()?->format('H:i:s'),
                ];
            },
            $businessHourRepository->findAll()
        );

        return new CreateAlarmOutOfHoursResponse($uuidClient, $businessHours);
    }

    /**
     * @param array<int, array<string, mixed>> $businessHours
     * @return array<int, array{
     *     day_of_week: int,
     *     start_time: ?\DateTimeImmutable,
     *     end_time: ?\DateTimeImmutable,
     *     start_time2: ?\DateTimeImmutable,
     *     end_time2: ?\DateTimeImmutable
     * }>
     */
    private function normalizeBusinessHours(array $businessHours): array
    {
        if ([] === $businessHours) {
            throw new \RuntimeException('INVALID_BUSINESS_HOURS');
        }

        $normalized = [];
        foreach ($businessHours as $index => $dayData) {
            if (!is_array($dayData)) {
                throw new \RuntimeException(sprintf('INVALID_BUSINESS_HOUR_ENTRY_AT_INDEX_%d', $index));
            }

            if (!array_key_exists('day_of_week', $dayData)) {
                throw new \RuntimeException('MISSING_DAY_OF_WEEK');
            }

            $dayOfWeek = (int) $dayData['day_of_week'];
            if ($dayOfWeek < 1 || $dayOfWeek > 7) {
                throw new \RuntimeException(sprintf('INVALID_DAY_OF_WEEK_%d', $dayOfWeek));
            }

            $startTime = $this->parseTime($dayData['start_time'] ?? null, sprintf('businessHours[%d].start_time', $index));
            $endTime = $this->parseTime($dayData['end_time'] ?? null, sprintf('businessHours[%d].end_time', $index));

            if ((null === $startTime) xor (null === $endTime)) {
                throw new \RuntimeException(sprintf('INCOMPLETE_TIME_RANGE_FOR_DAY_%d', $dayOfWeek));
            }

            $startTime2 = $this->parseTime($dayData['start_time2'] ?? null, sprintf('businessHours[%d].start_time2', $index));
            $endTime2 = $this->parseTime($dayData['end_time2'] ?? null, sprintf('businessHours[%d].end_time2', $index));

            if ((null === $startTime2) xor (null === $endTime2)) {
                throw new \RuntimeException(sprintf('INCOMPLETE_SECOND_TIME_RANGE_FOR_DAY_%d', $dayOfWeek));
            }

            $normalized[$dayOfWeek] = [
                'day_of_week' => $dayOfWeek,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'start_time2' => $startTime2,
                'end_time2' => $endTime2,
            ];
        }

        ksort($normalized);

        return array_values($normalized);
    }

    private function parseTime(mixed $value, string $field): ?\DateTimeImmutable
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new \RuntimeException(sprintf('INVALID_TIME_FORMAT_%s', $field));
        }

        $formats = ['H:i:s', 'H:i'];
        foreach ($formats as $format) {
            $dateTime = \DateTimeImmutable::createFromFormat($format, $value);
            if (false !== $dateTime) {
                return $dateTime;
            }
        }

        $this->logger->warning('Invalid time format received for business hours', [
            'field' => $field,
            'value' => $value,
        ]);

        throw new \RuntimeException(sprintf('INVALID_TIME_FORMAT_%s', $field));
    }
}
