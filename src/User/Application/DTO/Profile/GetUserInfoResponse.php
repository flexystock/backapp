<?php

namespace App\User\Application\DTO\Profile;

class GetUserInfoResponse
{
    private $userInfo;
    private $error;
    private $statusCode;

    public function __construct($userInfo = null, $error = null, $statusCode = 200)
    {
        $this->userInfo = $userInfo;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getUserInfo()
    {
        return $this->userInfo;
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