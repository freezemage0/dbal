<?php

namespace Freezemage\DBAL\Query;

use Freezemage\DBAL\Connection\ProcessorInterface;
use Freezemage\DBAL\Schema;

interface CompilerFactoryInterface
{
    public function create(Schema $schema, ProcessorInterface $processor): CompilerInterface;
}