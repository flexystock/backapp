<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\GetPendingAnomaliesRequest;
use App\Service\Merma\Application\DTO\GetPendingAnomaliesResponse;
use App\Service\Merma\Application\InputPorts\GetPendingAnomaliesUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientScaleEventRepository;
use Psr\Log\LoggerInterface;

final class GetPendingAnomaliesUseCase implements GetPendingAnomaliesUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {}

    public function execute(GetPendingAnomaliesRequest $request): GetPendingAnomaliesResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em        = $this->connectionManager->getEntityManager($client->getUuidClient());
        $eventRepo = new ClientScaleEventRepository($em);

        $events = $eventRepo->findAllPendingAnomalies();

        $anomalies = array_map(static function ($event): array {
            return [
                'id'           => $event->getId(),
                'scale_id'    => $event->getScale()->getId(),
                'scale_name' => $event->getScale()->getEndDeviceId(),
                'product_id'  => $event->getProduct()->getId(),
                'product_name' => $event->getProduct()->getName(),
                'weight_before' => $event->getWeightBefore(),
                'weight_after'  => $event->getWeightAfter(),
                'delta_kg'     => $event->getDeltaKg(),
                'delta_cost'   => $event->getDeltaCost(),
                'detected_at'  => $event->getDetectedAt()->format(\DateTimeInterface::ATOM),
                'notes'        => $event->getNotes(),
            ];
        }, $events);

        $this->logger->info('Pending anomalies retrieved', [
            'clientId' => $request->getUuidClient(),
            'count'    => count($anomalies),
        ]);

        return new GetPendingAnomaliesResponse($anomalies);
    }
}
