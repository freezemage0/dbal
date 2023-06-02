<?php

namespace Freezemage\DBAL\Schema;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class Table
{
    public function __construct(public string $name)
    {
    }
}