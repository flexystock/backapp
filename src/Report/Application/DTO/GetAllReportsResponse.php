<?php

namespace App\Report\Application\DTO;

class GetAllReportsResponse
{
    /**
     * @param array<int, array<string, mixed>> $reports
     */
    public function __construct(private readonly array $reports)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getReports(): array
    {
        return $this->reports;
    }
}
