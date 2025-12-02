<?php

namespace App\Message;

class GenerateScheduledReportMessage
{
    private string $tenantId;
    private int $reportId;

    public function __construct(string $tenantId, int $reportId)
    {
        $this->tenantId = $tenantId;
        $this->reportId = $reportId;
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function getReportId(): int
    {
        return $this->reportId;
    }
}