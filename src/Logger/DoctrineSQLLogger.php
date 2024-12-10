<?php

namespace App\Logger;

use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Log\LoggerInterface;

class DoctrineSQLLogger implements SQLLogger
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        $this->logger->debug('Doctrine SQL Query', [
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
        ]);
    }

    public function stopQuery()
    {
        // No es necesario implementar nada aquí
    }
}
