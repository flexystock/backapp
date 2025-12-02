<?php

namespace App\Report\Application\DTO;

class DeleteReportResponse
{
    private int $deletedReportId;

    public function __construct(int $deletedReportId)
    {
        $this->deletedReportId = $deletedReportId;
    }

    public function getDeletedReportId(): int
    {
        return $this->deletedReportId;
    }
}
