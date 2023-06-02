<?php

namespace Freezemage\DBAL\Connection;

interface ProcessorInterface
{
    public function escape(string $value): string;

    public function quote(string $identifier): string;
}