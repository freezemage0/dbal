<?php

namespace Freezemage\DBAL\Sqlite\Connection;

use Freezemage\DBAL\Connection\ResultInterface;
use SQLite3Result;

final class Result implements ResultInterface
{
    private SQLite3Result $result;

    public function __construct(SQLite3Result $result)
    {
        $this->result = $result;
    }

    public function fetchAll(): array
    {
        $result = [];
        while ($item = $this->fetch()) {
            $result[] = $item;
        }
        return $result;
    }

    /**
     * @return array<string, scalar>|null
     */
    public function fetch(): ?array
    {
        /** @var array<string, scalar>|false $data */
        $data = $this->result->fetchArray(SQLITE3_ASSOC);
        return $data ?: null;
    }
}