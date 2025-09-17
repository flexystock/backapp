<?php

namespace App\Client\Application\InputPorts;

use App\Entity\Main\Client;

interface GetInfoClientInputPort
{
    public function getInfo(string $uuidClient): Client;

}