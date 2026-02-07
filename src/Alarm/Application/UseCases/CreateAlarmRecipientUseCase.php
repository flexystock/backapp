<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\CreateAlarmRecipientRequest;
use App\Alarm\Application\InputPorts\CreateAlarmRecipientUseCaseInterface;
use App\Alarm\Application\OutputPorts\Repositories\AlarmTypeRecipientRepositoryInterface;
use App\Entity\Client\AlarmTypeRecipient;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class CreateAlarmRecipientUseCase implements CreateAlarmRecipientUseCaseInterface
{
    public function __construct(
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(CreateAlarmRecipientRequest $request): AlarmTypeRecipient
    {
        $uuidClient = $request->getUuidClient();

        // Switch to client database
        $this->connectionManager->switchConnection($uuidClient);
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);

        // Create repository
        $repository = new \App\Alarm\Infrastructure\OutputAdapters\Repositories\AlarmTypeRecipientRepository($entityManager);

        try {
            $recipient = $repository->addRecipient(
                $uuidClient,
                $request->getAlarmTypeId(),
                $request->getEmail(),
                $request->getUuidUser()
            );

            $this->logger->info('[CreateAlarmRecipientUseCase] Recipient added successfully.', [
                'uuidClient' => $uuidClient,
                'alarmTypeId' => $request->getAlarmTypeId(),
                'email' => $request->getEmail(),
            ]);

            return $recipient;
        } catch (\Exception $e) {
            $this->logger->error('[CreateAlarmRecipientUseCase] Error adding recipient.', [
                'exception' => $e->getMessage(),
                'uuidClient' => $uuidClient,
            ]);
            throw $e;
        }
    }
}
