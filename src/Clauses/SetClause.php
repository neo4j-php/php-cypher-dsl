<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

class SetClause extends Clause
{
    private array $patterns = [];

    public function addPattern(Pattern $pattern): void {
        $this->patterns[] = $pattern;
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
        $patterns = array_map(fn (Pattern $pattern): string => $pattern->toQuery(), $this->patterns);

        return implode(", ", $patterns);
    }
}