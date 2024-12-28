<?php

namespace App\Ttn\Application\InputPorts;

use App\Ttn\Application\DTO\GetAllTtnDevicesResponse;

interface GetAllTtnDevicesUseCaseInterface
{
    public function execute(): GetAllTtnDevicesResponse;

    public function executePaginated(int $page, int $limit, ?bool $available): GetAllTtnDevicesResponse;
}
