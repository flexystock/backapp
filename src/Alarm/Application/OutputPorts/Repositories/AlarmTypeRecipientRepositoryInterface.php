<?php

namespace App\Alarm\Application\OutputPorts\Repositories;

use App\Entity\Client\AlarmTypeRecipient;

interface AlarmTypeRecipientRepositoryInterface
{
    /**
     * Find all email addresses for a given client and alarm type
     *
     * @param string $uuidClient
     * @param int $alarmTypeId
     * @return string[]
     */
    public function findEmailsByClientAndType(string $uuidClient, int $alarmTypeId): array;

    /**
     * Add a new recipient for an alarm type
     *
     * @param string $uuidClient
     * @param int $alarmTypeId
     * @param string $email
     * @param string|null $uuidUserCreation
     * @return AlarmTypeRecipient
     */
    public function addRecipient(
        string $uuidClient,
        int $alarmTypeId,
        string $email,
        ?string $uuidUserCreation = null
    ): AlarmTypeRecipient;

    /**
     * Delete a recipient by ID
     *
     * @param int $id
     * @param string $uuidClient
     * @return bool
     */
    public function deleteRecipient(int $id, string $uuidClient): bool;

    /**
     * Find a recipient by ID
     *
     * @param int $id
     * @return AlarmTypeRecipient|null
     */
    public function findById(int $id): ?AlarmTypeRecipient;

    /**
     * Find all recipients for a client
     *
     * @param string $uuidClient
     * @return AlarmTypeRecipient[]
     */
    public function findByClient(string $uuidClient): array;

    /**
     * Find all recipients for a client and alarm type
     *
     * @param string $uuidClient
     * @param int $alarmTypeId
     * @return AlarmTypeRecipient[]
     */
    public function findByClientAndType(string $uuidClient, int $alarmTypeId): array;
}
