<?php

namespace App\Logger;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\ParameterType;
use Psr\Log\LoggerInterface;

/**
 * @method object getNativeConnection()
 */
class LoggingConnection implements Connection
{
    private Connection $connection;
    private LoggerInterface $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function prepare(string $sql): \Doctrine\DBAL\Driver\Statement
    {
        $this->logger->debug('Doctrine SQL Prepare', ['sql' => $sql]);
        return $this->connection->prepare($sql);
    }

    public function query(string $sql): \Doctrine\DBAL\Driver\Result
    {
        $this->logger->debug('Doctrine SQL Query', ['sql' => $sql]);
        return $this->connection->query($sql);
    }

    // Implementar todos los demás métodos delegando en $this->connection$ y logueando cuando corresponda.

    public function exec(string $sql): int
    {
        $this->logger->debug('Doctrine SQL Exec', ['sql' => $sql]);
        return $this->connection->exec($sql);
    }

    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        return $this->connection->commit();
    }

    public function rollBack()
    {
        return $this->connection->rollBack();
    }

    public function errorCode()
    {
        return $this->connection->errorCode();
    }

    public function errorInfo()
    {
        return $this->connection->errorInfo();
    }

    public function lastInsertId($name = null)
    {
        return $this->connection->lastInsertId($name);
    }

    public function getServerVersion()
    {
        return $this->connection->getServerVersion();
    }

    public function requiresQueryForServerVersion()
    {
        return $this->connection->requiresQueryForServerVersion();
    }

    public function quote($value, $type = ParameterType::STRING)
    {
        // TODO: Implement quote() method.
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method object getNativeConnection()
    }
}
