<?php

namespace App\Service\Merma\Application\OutputPorts;

use App\Entity\Client\ScaleEvent;

interface GetPendingAnomaliesRepositoryInterface
{
    /**
     * Returns all scale events with type='anomalia' and is_confirmed IS NULL.
     *
     * @return ScaleEvent[]
     */
    public function findAllPendingAnomalies(): array;
}
