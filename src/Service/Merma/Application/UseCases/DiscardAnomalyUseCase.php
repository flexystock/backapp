<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\DiscardAnomalyRequest;
use App\Service\Merma\Application\InputPorts\DiscardAnomalyUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientScaleEventRepository;
use Psr\Log\LoggerInterface;
use App\Entity\Client\ScaleEvent;

final class DiscardAnomalyUseCase implements DiscardAnomalyUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {}

    public function execute(DiscardAnomalyRequest $request): void
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em        = $this->connectionManager->getEntityManager($client->getUuidClient());
        $eventRepo = new ClientScaleEventRepository($em);

        $event = $eventRepo->findById($request->getEventId());
        if ($event === null) {
            throw new \RuntimeException('EVENT_NOT_FOUND');
        }

        $event->setIsConfirmed(false)
            ->setConfirmedAt(new \DateTime())
            ->setType(ScaleEvent::TYPE_CONSUMO); // ← añadir esta línea
        $eventRepo->save($event);

        $this->logger->info('Anomaly discarded', [
            'eventId'  => $request->getEventId(),
            'clientId' => $request->getUuidClient(),
        ]);
    }
}
