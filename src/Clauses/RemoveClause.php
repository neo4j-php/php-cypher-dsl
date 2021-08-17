<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Expression;

class RemoveClause extends Clause
{

    private Expression $expression;

    public function setExpression(Expression $expression): void {
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
        return $this->expression->toQuery();
    }
}