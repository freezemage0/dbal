<?php

namespace Freezemage\DBAL\Connection;

interface ResultInterface
{
    /**
     * @return array<string, scalar>|null
     */
    public function fetch(): ?array;

    public function fetchAll(): array;
}