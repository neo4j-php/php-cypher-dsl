<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Expression;

class LimitClause extends Clause
{
    /**
     * The expression of the limit statement
     * @var Expression $expression
     */
    private Expression $expression;

    /**
     * Sets the expression
     * @param Expression $expression
     */
    public function setExpression(Expression $expression): void {
        $this->expression = $expression;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "LIMIT";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if ( isset($this->expression) ) return $this->expression->toQuery();

        return "";
    }
}