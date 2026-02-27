<?php

namespace App\Ttn\Application\Services;

use App\Entity\Client\ClientConfig;
use App\Entity\Main\Client as MainClient;
use App\Ttn\Application\DTO\MinimumStockNotification;
use App\Ttn\Application\DTO\WeightVariationAlertNotification;
use App\Ttn\Application\OutputPorts\MinimumStockNotificationInterface;
use App\Ttn\Application\OutputPorts\WeightVariationAlertNotifierInterface;
use Doctrine\ORM\EntityManagerInterface;

class TtnAlarmNotificationService
{
    public function __construct(
        private readonly MinimumStockNotificationInterface $minimumStockNotifier,
        private readonly WeightVariationAlertNotifierInterface $weightVariationNotifier,
        private readonly EntityManagerInterface $mainEntityManager
    ) {
    }

    public function findMainClient(string $uuidClient): ?MainClient
    {
        return $this->mainEntityManager->getRepository(MainClient::class)->find($uuidClient);
    }

    /**
     * @return array{out_of_hours: bool, holidays: bool, battery_shelve: bool}
     */
    public function getNotificationSettings(EntityManagerInterface $entityManager): array
    {
        $clientConfig = $entityManager->getRepository(ClientConfig::class)->findOneBy([]);

        if (!$clientConfig instanceof ClientConfig) {
            return [
                'out_of_hours' => true,
                'holidays' => true,
                'battery_shelve' => true,
            ];
        }

        return [
            'out_of_hours' => $clientConfig->isCheckOutOfHours(),
            'holidays' => $clientConfig->isCheckHolidays(),
            'battery_shelve' => $clientConfig->isCheckBatteryShelve(),
        ];
    }

    /**
     * @return string[]
     */
    public function getRecipientEmailsForAlarmType(
        EntityManagerInterface $entityManager,
        string $uuidClient,
        int $alarmTypeId
    ): array {
        $query = $entityManager->createQuery(
            'SELECT atr.email FROM App\Entity\Client\AlarmTypeRecipient atr 
             JOIN atr.alarmType at
             WHERE atr.uuid_client = :uuidClient AND at.id = :alarmTypeId'
        );
        $query->setParameter('uuidClient', $uuidClient);
        $query->setParameter('alarmTypeId', $alarmTypeId);

        $result = $query->getResult();

        return array_map(fn ($row) => $row['email'], $result);
    }

    public function notifyWeightVariation(
        EntityManagerInterface $entityManager,
        string $uuidClient,
        MainClient $mainClient,
        int $productId,
        string $productName,
        int $scaleId,
        string $deviceId,
        float $previousWeightKg,
        float $newWeightKg,
        float $variationKg,
        float $weightRange,
        string $nameUnit,
        \DateTimeImmutable $occurredAt,
        bool $isHoliday,
        bool $isOutsideBusinessHours,
        int $alarmTypeId,
        ?float $conversionFactor = null
    ): void {
        $recipientEmails = $this->getRecipientEmailsForAlarmType($entityManager, $uuidClient, $alarmTypeId);

        $notification = new WeightVariationAlertNotification(
            $uuidClient,
            $mainClient->getClientName(),
            $recipientEmails,
            $productId,
            $productName,
            $scaleId,
            $deviceId,
            $previousWeightKg,
            $newWeightKg,
            $variationKg,
            $weightRange,
            $nameUnit,
            $occurredAt,
            $isHoliday,
            $isOutsideBusinessHours,
            $conversionFactor
        );

        $this->weightVariationNotifier->notify($notification);
    }

    public function notifyMinimumStock(
        EntityManagerInterface $entityManager,
        string $uuidClient,
        MainClient $mainClient,
        int $productId,
        string $productName,
        int $scaleId,
        string $deviceId,
        float $currentWeightKg,
        float $minimumStock,
        float $weightRange,
        string $nameUnit,
        ?float $conversionFactor
    ): void {
        $recipientEmails = $this->getRecipientEmailsForAlarmType($entityManager, $uuidClient, 1);

        $notification = new MinimumStockNotification(
            $uuidClient,
            $mainClient->getClientName(),
            $recipientEmails,
            $productId,
            $productName,
            $scaleId,
            $deviceId,
            $currentWeightKg,
            $minimumStock,
            $weightRange,
            $nameUnit,
            $conversionFactor
        );

        $this->minimumStockNotifier->notify($notification);
    }
}
