<?php

namespace App\Alarm\Application\UseCases;

use App\Alarm\Application\InputPorts\DeleteAlarmRecipientUseCaseInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class DeleteAlarmRecipientUseCase implements DeleteAlarmRecipientUseCaseInterface
{
    public function __construct(
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(int $id, string $uuidClient): bool
    {
        // Switch to client database
        $this->connectionManager->switchConnection($uuidClient);
        $entityManager = $this->connectionManager->getEntityManager($uuidClient);

        // Create repository
        $repository = new \App\Alarm\Infrastructure\OutputAdapters\Repositories\AlarmTypeRecipientRepository($entityManager);

        try {
            $result = $repository->deleteRecipient($id, $uuidClient);

            if ($result) {
                $this->logger->info('[DeleteAlarmRecipientUseCase] Recipient deleted successfully.', [
                    'id' => $id,
                    'uuidClient' => $uuidClient,
                ]);
            } else {
                $this->logger->warning('[DeleteAlarmRecipientUseCase] Recipient not found.', [
                    'id' => $id,
                    'uuidClient' => $uuidClient,
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            $this->logger->error('[DeleteAlarmRecipientUseCase] Error deleting recipient.', [
                'exception' => $e->getMessage(),
                'id' => $id,
                'uuidClient' => $uuidClient,
            ]);
            throw $e;
        }
    }
}
