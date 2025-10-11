<?php

namespace App\WeightAnalytics\Application\DTO;

class GetProductWeightSummaryResponse
{
    private $summary;
    private $error;
    private $statusCode;

    public function __construct($summary = null, $error = null, $statusCode = 200)
    {
        $this->summary = $summary;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
