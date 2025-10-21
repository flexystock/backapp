<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\CreateAlarmHolidayRequest;
use App\Alarm\Application\DTO\CreateAlarmHolidayResponse;
use App\Alarm\Application\InputPorts\CreateAlarmHolidayUseCaseInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\ClientConfigRepository;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\HolidayRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Holiday;
use App\Entity\Client\ClientConfig;
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
        $clientConfigRepository = new ClientConfigRepository($entityManager);

        $holidayDate = $this->parseHolidayDate($request->getHolidayDate());

        $holiday = $holidayRepository->findByHolidayDate($holidayDate);
        $timestamp = $request->getTimestamp() ?? new \DateTimeImmutable();
        $uuidUser = $request->getUuidUser() ?? 'system';
        if (!$holiday) {
            $holiday = new Holiday();
            $holiday->setHolidayDate($holidayDate);
            $holiday->setUuidUserCreation($uuidUser);
            $holiday->setDatehourCreation($timestamp);
        } else {
            $holiday->setHolidayDate($holidayDate);
            $holiday->setUuidUserModification($uuidUser);
            $holiday->setDatehourModification($timestamp);
        }

        $holiday->setName($request->getName());

        $holidayRepository->save($holiday);
        $holidayRepository->flush();

        $this->updateClientConfig(
            $clientConfigRepository,
            $request->isCheckHolidaysEnabled(),
            $uuidUser,
            $timestamp
        );

        $holidayData = [
            'id' => $holiday->getId(),
            'holiday_date' => $holiday->getHolidayDate()->format('Y-m-d'),
            'name' => $holiday->getName(),
        ];

        return new CreateAlarmHolidayResponse($holidayData);
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
            $clientConfig->setUuidUserModification($uuidUser);
            $clientConfig->setDatehourModification($timestamp);
        }

        $clientConfig->setCheckHolidays($isEnabled);

        $clientConfigRepository->save($clientConfig);
        $clientConfigRepository->flush();
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
