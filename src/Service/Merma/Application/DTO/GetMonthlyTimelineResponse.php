<?php

namespace App\Service\Merma\Application\DTO;

class GetMonthlyTimelineResponse
{
    public function __construct(
        public readonly array $events,
    ) {}
}
