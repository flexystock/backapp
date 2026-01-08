<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Infrastructure\OutputAdapters\Repositories;

use App\ControlPanel\Scale\Application\OutputPorts\ClientScalesRepositoryInterface;
use App\Entity\Client\Scales;
use App\Infrastructure\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class ClientScalesRepository implements ClientScalesRepositoryInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(
        ClientConnectionManager $connectionManager,
        LoggerInterface $logger
    ) {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function getVoltagePercentagesByClient(array $scalesByClient): array
    {
        $voltagePercentages = [];

        foreach ($scalesByClient as $clientUuid => $endDeviceIds) {
            if (empty($endDeviceIds)) {
                continue;
            }

            try {
                // Get the client's entity manager
                $clientEntityManager = $this->connectionManager->getEntityManager($clientUuid);

                // Query scales for this client
                $qb = $clientEntityManager->createQueryBuilder();
                $qb->select('s.end_device_id', 's.voltage_percentage')
                    ->from(Scales::class, 's')
                    ->where($qb->expr()->in('s.end_device_id', ':endDeviceIds'))
                    ->setParameter('endDeviceIds', $endDeviceIds);

                $results = $qb->getQuery()->getResult();

                // Map results to associative array
                foreach ($results as $result) {
                    $endDeviceId = $result['end_device_id'];
                    $voltagePercentage = $result['voltage_percentage'];
                    $voltagePercentages[$endDeviceId] = $voltagePercentage !== null ? (float) $voltagePercentage : null;
                }
            } catch (\Exception $e) {
                $this->logger->error("Error getting voltage percentages for client {$clientUuid}: {$e->getMessage()}");
                // Continue with other clients even if one fails
            }
        }

        return $voltagePercentages;
    }

    public function getLastSendTimestampsByClient(array $scalesByClient): array
    {
        $lastSendTimestamps = [];

        foreach ($scalesByClient as $clientUuid => $endDeviceIds) {
            if (empty($endDeviceIds)) {
                continue;
            }

            try {
                // Get the client's entity manager
                $clientEntityManager = $this->connectionManager->getEntityManager($clientUuid);

                // Query scales for this client
                $qb = $clientEntityManager->createQueryBuilder();
                $qb->select('s.end_device_id', 's.last_send')
                    ->from(Scales::class, 's')
                    ->where($qb->expr()->in('s.end_device_id', ':endDeviceIds'))
                    ->setParameter('endDeviceIds', $endDeviceIds);

                $results = $qb->getQuery()->getResult();

                // Map results to associative array
                foreach ($results as $result) {
                    $endDeviceId = $result['end_device_id'];
                    $lastSend = $result['last_send'];
                    $lastSendTimestamps[$endDeviceId] = $lastSend ? \DateTimeImmutable::createFromMutable($lastSend) : null;
                }
            } catch (\Exception $e) {
                $this->logger->error("Error getting last send timestamps for client {$clientUuid}: {$e->getMessage()}");
                // Continue with other clients even if one fails
            }
        }

        return $lastSendTimestamps;
    }
}
