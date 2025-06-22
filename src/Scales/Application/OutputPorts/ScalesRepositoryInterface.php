<?php

namespace App\Scales\Application\OutputPorts;

use App\Entity\Client\Scales;

interface ScalesRepositoryInterface
{
    public function selectClientConnection(string $uuidClient): void;
    public function save(Scales $scales): void;
    public function findOneBy(string $endDeviceId): ?Scales;
    public function findOneByProductId(int $productId): ?Scales;
    public function findByUuidAndClient(string $uuidScale, string $uuidClient): ?Scales;
    public function findAllByUuidClient(string $uuidClient): array;
    public function remove(Scales $scale): void;
}
