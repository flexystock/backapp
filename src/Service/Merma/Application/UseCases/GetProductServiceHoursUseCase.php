<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\GetProductServiceHoursRequest;
use App\Service\Merma\Application\InputPorts\GetProductServiceHoursUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientProductServiceHourRepository;
use Psr\Log\LoggerInterface;

final class GetProductServiceHoursUseCase implements GetProductServiceHoursUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {}

    public function execute(GetProductServiceHoursRequest $request): array
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em       = $this->connectionManager->getEntityManager($client->getUuidClient());
        $hourRepo = new ClientProductServiceHourRepository($em);

        $hours = $hourRepo->findByProductId($request->getProductId());

        $this->logger->info('ProductServiceHours retrieved', [
            'productId' => $request->getProductId(),
        ]);

        return array_map(static fn($h) => [
            'day_of_week'  => $h->getDayOfWeek(),
            'start_time_1' => $h->getStartTime1()->format('H:i'),
            'end_time_1'   => $h->getEndTime1()->format('H:i'),
            'start_time_2' => $h->getStartTime2()?->format('H:i'),
            'end_time_2'   => $h->getEndTime2()?->format('H:i'),
        ], $hours);
    }
}
