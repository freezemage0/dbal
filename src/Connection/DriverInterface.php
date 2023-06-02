<?php

namespace Freezemage\DBAL\Connection;

interface DriverInterface
{
    public function connect(): void;

    public function disconnect(): void;

    public function prepare(string $statement): StatementInterface;

    public function query(string $query): ?ResultInterface;
}