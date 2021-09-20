<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

class MergeClause extends Clause
{
    /**
     * @var Pattern $pattern
     */
    private Pattern $pattern;

    /**
     * @param Pattern $pattern
     */
    public function setPattern(Pattern $pattern): void
    {
        $this->pattern = $pattern;
    }
    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "MERGE";
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