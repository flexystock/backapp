<?php

namespace App\Logger;

use Doctrine\DBAL\Driver\API\ExceptionConverter;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Driver;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\VersionAwarePlatformDriver;
use Psr\Log\LoggerInterface;

class LoggingDriver implements Driver, VersionAwarePlatformDriver
{
    private Driver $driver;
    private LoggerInterface $logger;

    public function __construct(Driver $driver, LoggerInterface $logger)
    {
        $this->driver = $driver;
        $this->logger = $logger;
    }

    public function connect(array $params, $username = null, $password = null, ?array $driverOptions = null): Connection
    {
        $connection = $this->driver->connect($params, $username, $password, $driverOptions);

        return new LoggingConnection($connection, $this->logger);
    }

    public function getDatabasePlatform(): AbstractPlatform
    {
        return $this->driver->getDatabasePlatform();
    }

    public function getSchemaManager(Connection|\Doctrine\DBAL\Connection $conn, AbstractPlatform $platform)
    {
        return $this->driver->getSchemaManager($conn, $platform);
    }

    // Métodos adicionales de VersionAwarePlatformDriver si es necesario
    public function createDatabasePlatformForVersion($version)
    {
        if ($this->driver instanceof VersionAwarePlatformDriver) {
            return $this->driver->createDatabasePlatformForVersion($version);
        }

        return $this->getDatabasePlatform();
    }

    public function getName()
    {
        return $this->driver->getName();
    }

    public function getDatabase(Connection $conn)
    {
        return $this->driver->getDatabase($conn);
    }

    public function getExceptionConverter(): ExceptionConverter
    {
        // TODO: Implement getExceptionConverter() method.
    }
}
