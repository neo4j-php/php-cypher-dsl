<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

class LimitClause extends Clause
{
    /**
     * The expression of the limit statement
     * @var Pattern $pattern
     */
    private Pattern $pattern;

    /**
     * Sets the expression
     * @param Pattern $pattern
     */
    public function setExpression(Pattern $pattern): void {
        $this->pattern = $pattern;
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
        if ( isset($this->pattern) ) return $this->pattern->toQuery();

        return "";
    }
}