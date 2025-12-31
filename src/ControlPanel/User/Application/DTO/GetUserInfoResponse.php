<?php

namespace App\ControlPanel\User\Application\DTO;

class GetUserInfoResponse
{
    private $usersInfo;
    private $error;
    private $statusCode;

    public function __construct($usersInfo = null, $error = null, $statusCode = 200)
    {
        $this->usersInfo = $usersInfo;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getUsersInfo()
    {
        return $this->usersInfo;
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
