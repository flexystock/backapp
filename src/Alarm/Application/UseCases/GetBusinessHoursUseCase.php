<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\GetBusinessHoursRequest;
use App\Alarm\Application\DTO\GetBusinessHoursResponse;
use App\Alarm\Application\InputPorts\GetBusinessHoursUseCaseInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\BusinessHourRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\BusinessHour;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class GetBusinessHoursUseCase implements GetBusinessHoursUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(GetBusinessHoursRequest $request): GetBusinessHoursResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $entityManager = $this->connectionManager->getEntityManager($client->getUuidClient());
        $businessHourRepository = new BusinessHourRepository($entityManager);

        $businessHours = $businessHourRepository->findAll();

        $businessHoursData = array_map(
            fn (BusinessHour $businessHour): array => [
                'id' => $businessHour->getId(),
                'day_of_week' => $businessHour->getDayOfWeek(),
                'start_time' => $businessHour->getStartTime()->format('H:i:s'),
                'end_time' => $businessHour->getEndTime()->format('H:i:s'),
                'start_time2' => $businessHour->getStartTime2()?->format('H:i:s'),
                'end_time2' => $businessHour->getEndTime2()?->format('H:i:s'),
            ],
            $businessHours
        );

        $this->logger->info('Business hours retrieved for client', [
            'uuid_client' => $client->getUuidClient(),
            'count' => count($businessHoursData),
        ]);

        return new GetBusinessHoursResponse($businessHoursData);
    }
}
