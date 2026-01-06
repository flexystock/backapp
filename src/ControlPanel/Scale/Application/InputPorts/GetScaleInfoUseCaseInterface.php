<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\InputPorts;

use App\ControlPanel\Scale\Application\DTO\GetScaleInfoRequest;
use App\ControlPanel\Scale\Application\DTO\GetScaleInfoResponse;

interface GetScaleInfoUseCaseInterface
{
    /**
     * Retrieves scale information for the control panel.
     * If endDeviceId is provided, returns info for that specific scale.
     * If endDeviceId is null, returns info for all scales.
     *
     * @param GetScaleInfoRequest $request the request containing optional end device ID
     *
     * @return GetScaleInfoResponse the response containing scale(s) information
     */
    public function execute(GetScaleInfoRequest $request): GetScaleInfoResponse;
}
