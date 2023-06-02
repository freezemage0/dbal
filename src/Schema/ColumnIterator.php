<?php

namespace Freezemage\DBAL\Schema;

use ArrayIterator;
use Iterator;
use RuntimeException;

/**
 * @template-implements Iterator<string, Column>
 */
final class ColumnIterator implements Iterator
{
    private Iterator $innerIterator;

    public function __construct(Column ...$columns)
    {
        $this->innerIterator = new ArrayIterator($columns);
    }

    public function current(): Column
    {
        $current = $this->innerIterator->current();
        if (!($current instanceof Column)) {
            throw new RuntimeException('Wtf');
        }
        return $current;
    }

    public function next(): void
    {
        $this->innerIterator->next();
    }

    public function key(): int
    {
        return (int)$this->innerIterator->key();
    }

    public function valid(): bool
    {
        return $this->innerIterator->valid();
    }

    public function rewind(): void
    {
        $this->innerIterator->rewind();
    }
}