<?php

namespace App\Logger;

use Doctrine\DBAL\Driver\Driver; // Asegúrate que esta línea existe
use Doctrine\DBAL\Driver\Middleware;
use Psr\Log\LoggerInterface;

class DoctrineSQLMiddleware implements Middleware
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function wrap(Driver|\Doctrine\DBAL\Driver $driver): \Doctrine\DBAL\Driver
    {
        return $driver;
    }
}
