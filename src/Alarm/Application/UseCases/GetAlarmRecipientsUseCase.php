<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\InputPorts\GetAlarmRecipientsUseCaseInterface;
use App\Entity\Client\AlarmTypeRecipient;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class GetAlarmRecipientsUseCase implements GetAlarmRecipientsUseCaseInterface
{
    public function __construct(
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @param string $uuidClient
     * @param int|null $alarmTypeId
     * @return AlarmTypeRecipient[]
     */
    public function execute(string $uuidClient, ?int $alarmTypeId = null): array
    {
        // Switch to client database
        //$this->connectionManager->switchConnection($uuidClient);
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);

        // Create repository
        $repository = new \App\Alarm\Infrastructure\OutputAdapters\Repositories\AlarmTypeRecipientRepository($entityManager);

        try {
            if (null !== $alarmTypeId) {
                $recipients = $repository->findByClientAndType($uuidClient, $alarmTypeId);
            } else {
                $recipients = $repository->findByClient($uuidClient);
            }

            $this->logger->info('[GetAlarmRecipientsUseCase] Recipients retrieved successfully.', [
                'uuidClient' => $uuidClient,
                'alarmTypeId' => $alarmTypeId,
                'count' => count($recipients),
            ]);

            return $recipients;
        } catch (\Exception $e) {
            $this->logger->error('[GetAlarmRecipientsUseCase] Error retrieving recipients.', [
                'exception' => $e->getMessage(),
                'uuidClient' => $uuidClient,
            ]);
            throw $e;
        }
    }
}
