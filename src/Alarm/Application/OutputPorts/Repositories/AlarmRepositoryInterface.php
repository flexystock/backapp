<?php
namespace App\Alarm\Application\OutputPorts\Repositories;

use App\Entity\Client\AlarmConfig;

interface AlarmRepositoryInterface
{
    public function findByUuid(string $uuid): ?AlarmConfig;
    public function save(AlarmConfig $alarm): void;
    public function remove(AlarmConfig $alarm): void;
}
