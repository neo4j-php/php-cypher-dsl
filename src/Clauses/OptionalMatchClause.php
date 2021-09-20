<?php

/*
 * Cypher DSL
 * Copyright (C) 2021  Wikibase Solutions
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace WikibaseSolutions\CypherDSL\Clauses;

use WikibaseSolutions\CypherDSL\Expressions\Patterns\Pattern;

/**
 * This class represents an OPTIONAL MATCH clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/optional-match/
 */
class OptionalMatchClause extends Clause
{
    /**
     * @var Pattern[] List of patterns
     */
    private array $patterns = [];

    /**
     * Add a pattern to the optional match clause.
     *
     * @param  Pattern $pattern
     * @return OptionalMatchClause
     */
    public function addPattern(Pattern $pattern): self
    {
        $this->patterns[] = $pattern;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "OPTIONAL MATCH";
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