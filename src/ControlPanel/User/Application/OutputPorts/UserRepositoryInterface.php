<?php

declare(strict_types=1);

namespace App\ControlPanel\User\Application\OutputPorts;

use App\Entity\Main\User;

interface UserRepositoryInterface
{
    /**
     * Find all users in the database.
     *
     * @return array array of User entities
     */
    public function findAll(): array;

    /**
     * Find a user by their EMAIL.
     *
     * @param string $email the user's EMAIL
     *
     * @return User|null the User entity or null if not found
     */
    public function findOneByEmail(string $email): ?User;

    /**
     * Find a user by their UUID.
     *
     * @param string $uuidUser the user's UUID
     *
     * @return User|null the User entity or null if not found
     */
    public function findOneByUuid(string $uuidUser): ?User;
}
