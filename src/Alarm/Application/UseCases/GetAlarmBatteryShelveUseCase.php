<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\GetAlarmBatteryShelveRequest;
use App\Alarm\Application\DTO\GetAlarmBatteryShelveResponse;
use App\Alarm\Application\InputPorts\GetAlarmBatteryShelveUseCaseInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\ClientConfigRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\ClientConfig;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class GetAlarmBatteryShelveUseCase implements GetAlarmBatteryShelveUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(GetAlarmBatteryShelveRequest $request): GetAlarmBatteryShelveResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $entityManager = $this->connectionManager->getEntityManager($client->getUuidClient());
        $clientConfigRepository = new ClientConfigRepository($entityManager);

        $clientConfig = $clientConfigRepository->findConfig();

        if (!$clientConfig instanceof ClientConfig) {
            $this->logger->info('Client config not found for battery shelve alarm, returning defaults', [
                'uuidClient' => $client->getUuidClient(),
            ]);

            return new GetAlarmBatteryShelveResponse(true);
        }

        return new GetAlarmBatteryShelveResponse($clientConfig->isCheckBatteryShelve());
    }
}
