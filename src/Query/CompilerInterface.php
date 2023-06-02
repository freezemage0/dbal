<?php

namespace Freezemage\DBAL\Query;

use Freezemage\DBAL\Query\Criteria\Comparison;
use Freezemage\DBAL\Query\Criteria\InRange;
use Freezemage\DBAL\Query\Criteria\Logical;
use Freezemage\DBAL\Schema\Definition;
use Freezemage\DBAL\Schema\Table;

interface CompilerInterface
{
    public function compileLogical(Logical $logical): string;

    public function compileComparison(Comparison $comparison): string;

    public function compileRange(InRange $inRange): string;

    public function compileNavigation(Navigation $navigation): string;

    public function compileDefinition(Definition $definition): string;

    public function compileTable(Table $table): string;
}