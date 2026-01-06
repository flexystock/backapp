<?php

declare(strict_types=1);

namespace App\ControlPanel\User\Application\InputPorts;

use App\ControlPanel\User\Application\DTO\GetUserInfoRequest;
use App\ControlPanel\User\Application\DTO\GetUserInfoResponse;

interface GetUserInfoUseCaseInterface
{
    /**
     * Retrieves user information for the control panel.
     * If emailUser is provided, returns info for that specific user.
     * If emailUser is null, returns info for all users.
     *
     * @param GetUserInfoRequest $request the request containing optional user EMAIL
     *
     * @return GetUserInfoResponse the response containing user(s) information
     */
    public function execute(GetUserInfoRequest $request): GetUserInfoResponse;
}
