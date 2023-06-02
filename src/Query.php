<?php

namespace Freezemage\DBAL;

use Freezemage\DBAL\Query\CompilerInterface;
use Freezemage\DBAL\Query\Criteria;
use Freezemage\DBAL\Query\Criterion;
use Freezemage\DBAL\Query\Navigation;
use Freezemage\DBAL\Schema\Column;

final class Query
{
    private Criteria $criteria;
    private Navigation $navigation;

    public function __construct(Criteria $criteria = null, Navigation $navigation = null)
    {
        $this->criteria = $criteria ?? new Criteria();
        $this->navigation = $navigation ?? new Navigation();
    }

    public function where(Criterion $criterion): Query
    {
        $this->criteria->where($criterion);
        return $this;
    }

    public function whereOr(Criterion $criterion): Query
    {
        $this->criteria->whereOr($criterion);
        return $this;
    }

    public function setLimit(int $limit): Query
    {
        $this->navigation->limit = $limit;
        return $this;
    }

    public function setOffset(int $offset): Query
    {
        $this->navigation->offset = $offset;
        return $this;
    }

    public function compile(CompilerInterface $compiler, Schema $schema): string
    {
        /** @var array<array-key, string> $columns */
        $columns = $schema->getColumns()->map(
            fn(Column $column): string => $compiler->compileDefinition($schema->getColumnDefinition($column->name))
        );

        $columns = implode(', ', $columns);
        $query = "SELECT {$columns}\nFROM\n\t{$compiler->compileTable($schema->getTable())}";
        if (!$this->criteria->isEmpty()) {
            $query .= "\nWHERE {$this->criteria->compile($compiler)}";
        }

        return $query . $compiler->compileNavigation($this->navigation) . ';';
    }
}