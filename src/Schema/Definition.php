<?php

namespace Freezemage\DBAL\Schema;

final class Definition
{
    private Table $table;
    private Column $column;
    private string $alias;

    public function __construct(Table $table, Column $column)
    {
        $this->table = $table;
        $this->column = $column;
        $this->alias = "{$this->table->name}_{$this->column->name}";
    }

    public function getFullDefinition(): string
    {
        return "{$this->table->name}.{$this->column->name}";
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }
}