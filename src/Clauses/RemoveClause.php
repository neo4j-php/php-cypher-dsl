<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

class RemoveClause extends Clause
{
    /**
     * @var Expression $expression
     */
    private Expression $expression;

    /**
     * @param Expression $expression
     */
    public function setExpression(Expression $expression): void
    {
        $this->expression = $expression;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "REMOVE";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (!isset($this->expression)) {
            return "";
        }

        return $this->expression->toQuery();
    }
}