<?php

namespace App\Client\Application\InputPorts;
use App\Client\Application\DTO\UpdateInfoClientRequest;
use App\Client\Application\DTO\UpdateInfoClientResponse;

interface UpdateInfoClientInputPort
{
    public function execute(UpdateInfoClientRequest $request): UpdateInfoClientResponse;

}