<?php

namespace Freezemage\DBAL\Query;

interface Criterion
{
    public function compile(CompilerInterface $compiler): string;
}