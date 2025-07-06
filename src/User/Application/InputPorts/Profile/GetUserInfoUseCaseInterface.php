<?php

namespace App\User\Application\InputPorts\Profile;
use App\User\Application\DTO\Profile\GetUserInfoRequest;
use App\User\Application\DTO\Profile\GetUserInfoResponse;
interface GetUserInfoUseCaseInterface
{
    /**
     * Retrieves user information based on the provided client and user UUIDs.
     *
     * @param string $uuidClient The UUID of the client.
     * @param string $uuidUser The UUID of the user.
     * @return array An associative array containing user information.
     */
    public function execute(GetUserInfoRequest $request): GetUserInfoResponse;

}