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

use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Patterns\CompletePattern;

/**
 * This class represents an OPTIONAL MATCH clause.
 *
 * The OPTIONAL MATCH clause is used to search for the pattern described in it, while using nulls for missing parts
 * of the pattern.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/optional-match/
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 69)
 */
final class OptionalMatchClause extends Clause
{
    /**
     * @var CompletePattern[] List of patterns
     */
    private array $patterns = [];

    /**
     * Add one or more patterns to the OPTIONAL MATCH clause.
     *
     * @param CompletePattern ...$pattern
     * @return $this
     */
    public function addPattern(CompletePattern ...$pattern): self
    {
        $this->patterns = array_merge($this->patterns, $pattern);

        return $this;
    }

    /**
     * Returns the patterns to optionally match.
     *
     * @return CompletePattern[]
     */
    public function getPatterns(): array
    {
        return $this->patterns;
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
        return implode(
            ", ",
            array_map(fn (CompletePattern $pattern): string => $pattern->toQuery(), $this->patterns)
        );
    }
}
