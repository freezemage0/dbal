<?php

namespace Freezemage\DBAL\Query\Criteria;

use Freezemage\DBAL\Query\CompilerInterface;
use Freezemage\DBAL\Query\Criterion;
use UnexpectedValueException;

final class InRange implements Criterion
{
    private string $definition;
    private array $range;

    public function __construct(string $definition, array $range)
    {
        if (empty($range)) {
            throw new UnexpectedValueException('Cannot create criterion: empty range');
        }
        $this->definition = $definition;
        $this->range = $range;
    }

    public function getDefinition(): string
    {
        return $this->definition;
    }

    public function getRange(): array
    {
        return $this->range;
    }

    public function compile(CompilerInterface $compiler): string
    {
        return $compiler->compileRange($this);
    }
}