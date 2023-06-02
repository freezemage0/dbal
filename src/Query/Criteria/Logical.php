<?php

namespace Freezemage\DBAL\Query\Criteria;

use Freezemage\DBAL\Query\CompilerInterface;
use Freezemage\DBAL\Query\Criterion;
use OutOfRangeException;

final class Logical implements Criterion
{
    public const AND = 'AND';
    public const OR = 'OR';
    /** @var array<string, Logical> */
    private static array $storage = [];
    private string $type;

    private function __construct(string $type)
    {
        if (!Logical::isValidType($type)) {
            throw new OutOfRangeException("{$type} is not a valid logical criterion.");
        }
        $this->type = $type;
    }

    private static function isValidType(string $type): bool
    {
        return in_array($type, [Logical::AND, Logical::OR], true);
    }

    public static function createAnd(): Logical
    {
        return Logical::create(Logical::AND);
    }

    private static function create(string $type): Logical
    {
        if (!isset(Logical::$storage[$type])) {
            Logical::$storage[$type] = new Logical($type);
        }
        return Logical::$storage[$type];
    }

    public static function createOr(): Logical
    {
        return Logical::create(Logical::OR);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function compile(CompilerInterface $compiler): string
    {
        return $compiler->compileLogical($this);
    }
}