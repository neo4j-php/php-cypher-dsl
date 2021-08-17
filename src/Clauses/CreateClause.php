<?php

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

class CreateClause extends Clause
{
    /**
     * @var Pattern[] list of patterns
     */
    private array $patterns = [];

    /**
     * Add pattern to the create clause
     * @param Pattern $pattern
     * @return $this
     */
    public function addPattern(Pattern $pattern): self {
        $this->patterns[] = $pattern;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "CREATE";
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