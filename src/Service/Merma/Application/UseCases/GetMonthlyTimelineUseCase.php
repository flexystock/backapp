<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\GetMonthlyTimelineRequest;
use App\Service\Merma\Application\DTO\GetMonthlyTimelineResponse;
use App\Service\Merma\Application\InputPorts\GetMonthlyTimelineUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientScaleEventRepository;
use Psr\Log\LoggerInterface;

final class GetMonthlyTimelineUseCase implements GetMonthlyTimelineUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {}

    public function execute(GetMonthlyTimelineRequest $request): GetMonthlyTimelineResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em        = $this->connectionManager->getEntityManager($client->getUuidClient());
        $eventRepo = new ClientScaleEventRepository($em);

        $start = new \DateTime('first day of this month 00:00:00');
        $end   = new \DateTime();

        $rawEvents = $eventRepo->findTimelineEvents(
            $request->getScaleId(),
            $request->getProductId(),
            $start,
            $end
        );

        $events = array_map(static function ($event): array {
            return [
                'id'           => $event->getId(),
                'tipo'         => $event->getType(),
                'peso'         => (float) $event->getWeightAfter(),
                'delta_kg'     => $event->getDeltaKg(),
                'delta_cost'   => $event->getDeltaCost(),
                'detected_at'  => $event->getDetectedAt()->format(\DateTimeInterface::ATOM),
                'is_confirmed' => $event->getIsConfirmed(),
                'notes'        => $event->getNotes(),
            ];
        }, $rawEvents);

        $this->logger->info('Monthly timeline retrieved', [
            'clientId'  => $request->getUuidClient(),
            'scaleId'   => $request->getScaleId(),
            'productId' => $request->getProductId(),
            'count'     => count($events),
        ]);

        return new GetMonthlyTimelineResponse($events);
    }
}
