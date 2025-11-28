<?php

namespace App\Report\Application\OutputPorts;

use App\Entity\Client\Report;

interface ReportRepositoryInterface
{
    public function findById(int $id): ?Report;

    /**
     * @return array<int, Report>
     */
    public function findAll(): array;

    public function save(Report $report): void;

    public function remove(Report $report): void;

    public function flush(): void;

    public function count(): int;
}
