<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\GetHolidaysRequest;
use App\Alarm\Application\DTO\GetHolidaysResponse;
use App\Alarm\Application\InputPorts\GetHolidaysUseCaseInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\HolidayRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Holiday;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class GetHolidaysUseCase implements GetHolidaysUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(GetHolidaysRequest $request): GetHolidaysResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $entityManager = $this->connectionManager->getEntityManager($client->getUuidClient());
        $holidayRepository = new HolidayRepository($entityManager);

        $holidays = $holidayRepository->findAll();

        $holidaysData = array_map(
            fn (Holiday $holiday): array => [
                'id' => $holiday->getId(),
                'holiday_date' => $holiday->getHolidayDate()->format('Y-m-d'),
                'name' => $holiday->getName(),
            ],
            $holidays
        );

        $this->logger->info('Holidays retrieved for client', [
            'uuid_client' => $client->getUuidClient(),
            'count' => count($holidaysData),
        ]);

        return new GetHolidaysResponse($holidaysData);
    }
}
