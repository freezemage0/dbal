<?php

namespace Freezemage\DBAL\Schema;

use Attribute;
use RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Column
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly bool $isAutoincrement = false,
        public readonly bool $isPrimary = false,
        public readonly bool $isNullable = true,
        public ?string $propertyName = null
    ) {
    }

    public function property(): string
    {
        if (!isset($this->propertyName)) {
            throw new RuntimeException('Undefined property name.');
        }

        return $this->propertyName;
    }
}