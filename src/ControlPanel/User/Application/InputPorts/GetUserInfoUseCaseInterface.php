<?php

namespace App\ControlPanel\User\Application\InputPorts;

use App\ControlPanel\User\Application\DTO\GetUserInfoRequest;
use App\ControlPanel\User\Application\DTO\GetUserInfoResponse;

interface GetUserInfoUseCaseInterface
{
    /**
     * Retrieves user information for the control panel.
     * If uuidUser is provided, returns info for that specific user.
     * If uuidUser is null, returns info for all users.
     *
     * @param GetUserInfoRequest $request the request containing optional user UUID
     *
     * @return GetUserInfoResponse the response containing user(s) information
     */
    public function execute(GetUserInfoRequest $request): GetUserInfoResponse;
}
