<?php

namespace App\Report\Application\DTO;

class GetReportResponse
{
    /**
     * @var array<string, mixed>
     */
    private array $report;

    public function __construct(array $report)
    {
        $this->report = $report;
    }

    /**
     * @return array<string, mixed>
     */
    public function getReport(): array
    {
        return $this->report;
    }
}
