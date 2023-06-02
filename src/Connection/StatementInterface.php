<?php

namespace Freezemage\DBAL\Connection;

interface StatementInterface
{
    /**
     * @param string $placeholder
     * @param scalar $value
     * @return void
     */
    public function bind(string $placeholder, float|bool|int|string $value): void;

    public function execute(): ?ResultInterface;
}