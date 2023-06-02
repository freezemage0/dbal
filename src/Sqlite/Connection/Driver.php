<?php

namespace Freezemage\DBAL\Sqlite\Connection;

use Freezemage\DBAL\Connection\DriverInterface;
use Freezemage\DBAL\Connection\ProcessorInterface;
use Freezemage\DBAL\Connection\ResultInterface;
use Freezemage\DBAL\Connection\StatementInterface;
use RuntimeException;
use SQLite3;

final class Driver implements DriverInterface, ProcessorInterface
{
    private string $databasePath;
    private ?SQLite3 $driver = null;

    public function __construct(string $databasePath)
    {
        $this->databasePath = $databasePath;
    }

    public function query(string $query): ?ResultInterface
    {
        $result = $this->driver()->query($query);
        if (!$result) {
            throw new RuntimeException($this->driver()->lastErrorMsg(), $this->driver()->lastErrorCode());
        }

        return new Result($result);
    }

    private function driver(): SQLite3
    {
        $this->connect();
        if (!isset($this->driver)) {
            throw new RuntimeException('Failed to connect to database.');
        }
        return $this->driver;
    }

    public function connect(): void
    {
        if (!isset($this->driver)) {
            $this->driver = new SQLite3($this->databasePath);
        }
    }

    public function prepare(string $statement): StatementInterface
    {
        $statement = $this->driver()->prepare($statement);
        if (!$statement) {
            // todo: custom runtime exception
            throw new RuntimeException($this->driver()->lastErrorMsg(), $this->driver()->lastErrorCode());
        }

        return new Statement($statement);
    }

    public function escape(string $value): string
    {
        $value = SQLite3::escapeString($value);
        if (!is_numeric($value)) {
            $value = "'{$value}'";
        }
        return $value;
    }

    public function quote(string $identifier): string
    {
        $parts = explode('.', $identifier);
        $parts = array_map(fn(string $part): string => '"' . trim($part, '"') . '"', $parts);
        return implode('.', $parts);
    }

    public function disconnect(): void
    {
        if (isset($this->driver)) {
            $this->driver->close();
            unset($this->driver);
        }
    }
}