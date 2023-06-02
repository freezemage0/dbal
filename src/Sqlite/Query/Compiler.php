<?php

namespace Freezemage\DBAL\Sqlite\Query;

use Freezemage\DBAL\Connection\ProcessorInterface;
use Freezemage\DBAL\Query\CompilerInterface;
use Freezemage\DBAL\Query\Criteria\Comparison;
use Freezemage\DBAL\Query\Criteria\InRange;
use Freezemage\DBAL\Query\Criteria\Logical;
use Freezemage\DBAL\Query\Navigation;
use Freezemage\DBAL\Schema;
use Freezemage\DBAL\Schema\Definition;
use Freezemage\DBAL\Schema\Table;

final class Compiler implements CompilerInterface
{
    private ProcessorInterface $processor;
    private Schema $schema;

    public function __construct(Schema $schema, ProcessorInterface $processor)
    {
        $this->processor = $processor;
        $this->schema = $schema;
    }

    public function compileLogical(Logical $logical): string
    {
        return $logical->getType();
    }

    public function compileComparison(Comparison $comparison): string
    {
        $definition = $this->schema->getColumnDefinition($comparison->getDefinition());
        $value = $this->processor->escape($comparison->getValue());

        return "\n\t{$this->processor->quote($definition->getAlias())} {$comparison->getOperator()} {$value}";
    }

    public function compileNavigation(Navigation $navigation): string
    {
        $compiled = '';
        if (isset($navigation->limit)) {
            $compiled .= "LIMIT {$navigation->limit}";
            if (isset($navigation->offset)) {
                $compiled .= " OFFSET {$navigation->offset}";
            }
        }

        return $compiled;
    }

    public function compileDefinition(Definition $definition): string
    {
        $full = $this->processor->quote($definition->getFullDefinition());
        $alias = $this->processor->quote($definition->getAlias());

        return "\n\t{$full} AS {$alias}";
    }

    public function compileTable(Table $table): string
    {
        return $this->processor->quote($table->name);
    }

    public function compileRange(InRange $inRange): string
    {
        $definition = $this->schema->getColumnDefinition($inRange->getDefinition());
        $range = implode(', ', array_map($this->processor->escape(...), $inRange->getRange()));

        return "\n\t{$this->processor->quote($definition->getAlias())} IN ({$range})";
    }
}