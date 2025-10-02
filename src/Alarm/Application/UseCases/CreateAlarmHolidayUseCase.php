<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\CreateAlarmHolidayRequest;
use App\Alarm\Application\DTO\CreateAlarmHolidayResponse;
use App\Alarm\Application\InputPorts\CreateAlarmHolidayUseCaseInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\HolidayRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Holiday;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class CreateAlarmHolidayUseCase implements CreateAlarmHolidayUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(CreateAlarmHolidayRequest $request): CreateAlarmHolidayResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $uuidClient = $client->getUuidClient();
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);
        $holidayRepository = new HolidayRepository($entityManager);

        $holidayDate = $this->parseHolidayDate($request->getHolidayDate());

        $holiday = $holidayRepository->findByHolidayDate($holidayDate);
        if (!$holiday) {
            $holiday = new Holiday();
            $holiday->setHolidayDate($holidayDate);
            $holiday->setUuidUserCreation($request->getUuidUser() ?? 'system');
            $holiday->setDatehourCreation($request->getTimestamp() ?? new \DateTimeImmutable());
        } else {
            $holiday->setHolidayDate($holidayDate);
            $holiday->setUuidUserModification($request->getUuidUser());
            $holiday->setDatehourModification($request->getTimestamp() ?? new \DateTimeImmutable());
        }

        $holiday->setName($request->getName());

        $holidayRepository->save($holiday);
        $holidayRepository->flush();

        $holidayData = [
            'id' => $holiday->getId(),
            'holiday_date' => $holiday->getHolidayDate()->format('Y-m-d'),
            'name' => $holiday->getName(),
        ];

        return new CreateAlarmHolidayResponse($holidayData);
    }

    private function parseHolidayDate(string $holidayDate): \DateTimeImmutable
    {
        $normalizedDate = \DateTimeImmutable::createFromFormat('Y-m-d', $holidayDate);
        if (false !== $normalizedDate) {
            return $normalizedDate->setTime(0, 0);
        }

        try {
            $dateTime = new \DateTimeImmutable($holidayDate);

            return $dateTime->setTime(0, 0);
        } catch (\Exception $exception) {
            $this->logger->error('Invalid holiday date received', [
                'holidayDate' => $holidayDate,
                'exception' => $exception->getMessage(),
            ]);

            throw new \RuntimeException('INVALID_HOLIDAY_DATE');
        }
    }
}
