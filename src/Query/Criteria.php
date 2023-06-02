<?php

namespace Freezemage\DBAL\Query;

use Freezemage\DBAL\Query\Criteria\Logical;

final class Criteria
{
    /** @var array<Criterion> */
    private array $criteria = [];

    public function where(Criterion $criterion): Criteria
    {
        $this->criteria[] = $criterion;
        $this->criteria[] = Logical::createAnd();

        return $this;
    }

    public function whereOr(Criterion $criterion): Criteria
    {
        $this->criteria[] = $criterion;
        $this->criteria[] = Logical::createOr();

        return $this;
    }

    public function logic(Logical $logical): Criteria
    {
        $lastCriterion = array_pop($this->criteria);

        if ($lastCriterion instanceof Logical) {
            $lastCriterion = $logical;
        }

        $this->criteria[] = $lastCriterion;

        return $this;
    }

    public function isEmpty(): bool
    {
        return empty($this->criteria);
    }

    public function compile(CompilerInterface $compiler): string
    {
        $lastCriterion = array_pop($this->criteria);
        if (!($lastCriterion instanceof Logical)) {
            $this->criteria[] = $lastCriterion;
        }

        /** @var array<string> $parts */
        $parts = array_reduce(
            $this->criteria,
            static function (array $parts, ?Criterion $criterion) use ($compiler): array {
                if (!empty($criterion)) {
                    $parts[] = $criterion->compile($compiler);
                }
                return $parts;
            },
            []
        );

        return implode(' ', $parts);
    }

    private function getLastCriterion(): ?Criterion
    {
        if (empty($this->criteria)) {
            return null;
        }

        $criterion = array_pop($this->criteria);
        return $this->criteria[] = $criterion;
    }
}