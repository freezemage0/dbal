<?php

namespace Freezemage\DBAL\Schema;

use DomainException;
use IteratorAggregate;

/**
 * @template-implements IteratorAggregate<int, Column>
 */
final class ColumnCollection implements IteratorAggregate
{
    /** @var Column[] */
    private array $columns;

    public function __construct(Column ...$columns)
    {
        $this->columns = $columns;
    }

    public function __clone()
    {
        $this->columns = array_map(fn(Column $column): Column => clone $column, $this->columns);
    }

    public function withColumn(Column $column): ColumnCollection
    {
        if (in_array($column, $this->columns, true)) {
            throw new DomainException('Unable to attach column: already exists.');
        }

        $clone = clone $this;
        $clone->columns[] = $column;

        return $clone;
    }

    public function withoutColumn(Column $column): ColumnCollection
    {
        $clone = clone $this;

        $index = array_search($column, $this->columns, true);
        if ($index !== false) {
            unset($clone->columns[$index]);
        }

        return $clone;
    }

    public function map(callable $mapper): array
    {
        return array_map($mapper, $this->columns);
    }

    public function findByName(string $name): ?Column
    {
        return $this->find(fn(Column $column): bool => $column->name === $name);
    }

    public function find(callable $finder): ?Column
    {
        foreach ($this->columns as $column) {
            if ($finder($column) === true) {
                return $column;
            }
        }

        return null;
    }

    public function getIterator(): ColumnIterator
    {
        return new ColumnIterator(...$this->columns);
    }
}