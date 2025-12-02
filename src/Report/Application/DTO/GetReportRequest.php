<?php

namespace App\Report\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GetReportRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_REPORT_ID')]
    #[Assert\Positive(message: 'INVALID_REPORT_ID')]
    private int $reportId;

    public function __construct(string $uuidClient, int $reportId)
    {
        $this->uuidClient = $uuidClient;
        $this->reportId = $reportId;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getReportId(): int
    {
        return $this->reportId;
    }
}
