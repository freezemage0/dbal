<?php

namespace Freezemage\DBAL\Sqlite\Connection;

use Freezemage\DBAL\Connection\ResultInterface;
use Freezemage\DBAL\Connection\StatementInterface;
use SQLite3Stmt;

final class Statement implements StatementInterface
{
    private SQLite3Stmt $statement;

    public function __construct(SQLite3Stmt $statement)
    {
        $this->statement = $statement;
    }

    public function bind(string $placeholder, float|bool|int|string $value): void
    {
        $type = match (gettype($value)) {
            'string' => SQLITE3_TEXT,
            'double' => SQLITE3_FLOAT,
            'integer' => SQLITE3_INTEGER,
            default => SQLITE3_NULL
        };

        $this->statement->bindValue($placeholder, $value, $type);
    }

    public function execute(): ?ResultInterface
    {
        $result = $this->statement->execute();
        return ($result != null) ? new Result($result) : null;
    }
}