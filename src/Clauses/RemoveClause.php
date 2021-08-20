<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

class RemoveClause extends Clause
{

    private Pattern $pattern;

    public function setPattern(Pattern $pattern): void {
        $this->pattern = $pattern;
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
        if ( isset($this->pattern) ) return $this->pattern->toQuery();

        return "";
    }
}