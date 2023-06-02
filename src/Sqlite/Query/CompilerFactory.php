<?php

namespace Freezemage\DBAL\Sqlite\Query;

use Freezemage\DBAL\Connection\ProcessorInterface;
use Freezemage\DBAL\Query\CompilerFactoryInterface;
use Freezemage\DBAL\Query\CompilerInterface;
use Freezemage\DBAL\Schema;

class CompilerFactory implements CompilerFactoryInterface
{
    public function create(Schema $schema, ProcessorInterface $processor): CompilerInterface
    {
        return new Compiler($schema, $processor);
    }
}