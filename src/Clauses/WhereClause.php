<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

class WhereClause extends Clause
{
    private Pattern $pattern;

    public function setPattern(Pattern $pattern): void
    {
        $this->pattern = $pattern;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "WHERE";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        if (isset($this->pattern) ) { return $this->pattern->toQuery();
        }

        return "";
    }
}