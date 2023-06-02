<?php

namespace Freezemage\DBAL\Query\Criteria;

use Freezemage\DBAL\Query\CompilerInterface;
use Freezemage\DBAL\Query\Criterion;

class Comparison implements Criterion
{
    private string $definition;
    private string $operator;
    private string $value;

    public function __construct(string $definition, string $operator, string $value)
    {
        $this->definition = $definition;
        $this->operator = $operator;
        $this->value = $value;
    }

    final public function getDefinition(): string
    {
        return $this->definition;
    }

    final public function getOperator(): string
    {
        return $this->operator;
    }

    final public function getValue(): string
    {
        return $this->value;
    }

    public function compile(CompilerInterface $compiler): string
    {
        return $compiler->compileComparison($this);
    }
}