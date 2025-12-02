<?php

namespace App\Report\Application\DTO;

class GetInfoToDashBoardResponse
{
    /**
     * @var array<string, mixed>
     */
    private array $dashboardInfo;

    public function __construct(array $dashboardInfo)
    {
        $this->dashboardInfo = $dashboardInfo;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDashboardInfo(): array
    {
        return $this->dashboardInfo;
    }
}
