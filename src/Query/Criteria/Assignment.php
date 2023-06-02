<?php

namespace Freezemage\DBAL\Query\Criteria;

final class Assignment extends Comparison
{
    public function __construct(string $definition, string $value)
    {
        parent::__construct($definition, '=', $value);
    }
}