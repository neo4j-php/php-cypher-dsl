<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

class SetClause extends Clause
{
    /**
     * @var Expression[] $expressions
     */
    private array $expressions = [];

    public function addExpression(Expression $expression): void
    {
        $this->expressions[] = $expression;
    }
    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "SET";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        $expressions = array_map(fn (Expression $expression): string => $expression->toQuery(), $this->expressions);

        return implode(", ", $expressions);
    }
}