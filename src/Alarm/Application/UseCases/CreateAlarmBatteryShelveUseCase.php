<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\CreateAlarmBatteryShelveRequest;
use App\Alarm\Application\DTO\CreateAlarmBatteryShelveResponse;
use App\Alarm\Application\InputPorts\CreateAlarmBatteryShelveUseCaseInterface;
use App\Alarm\Infrastructure\OutputAdapters\Repositories\ClientConfigRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\ClientConfig;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class CreateAlarmBatteryShelveUseCase implements CreateAlarmBatteryShelveUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(CreateAlarmBatteryShelveRequest $request): CreateAlarmBatteryShelveResponse
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $uuidClient = $client->getUuidClient();
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);
        $clientConfigRepository = new ClientConfigRepository($entityManager);

        $timestamp = $request->getTimestamp() ?? new \DateTimeImmutable();
        $uuidUser = $request->getUuidUser() ?? 'system';

        $clientConfig = $clientConfigRepository->findConfig();
        $isNewConfig = false;

        if (!$clientConfig) {
            $clientConfig = (new ClientConfig())
                ->setUuidUserCreation($uuidUser)
                ->setDatehourCreation($timestamp);
            $isNewConfig = true;
        } else {
            $clientConfig->setUuidUserModification($uuidUser);
            $clientConfig->setDatehourModification($timestamp);
        }

        $clientConfig->setCheckBatteryShelve($request->isCheckBatteryShelveEnabled());

        $clientConfigRepository->save($clientConfig);
        $clientConfigRepository->flush();

        if ($isNewConfig) {
            $this->logger->info('Created client config for battery shelve alarm', [
                'uuidClient' => $uuidClient,
            ]);
        }

        return new CreateAlarmBatteryShelveResponse($clientConfig->isCheckBatteryShelve());
    }
}
