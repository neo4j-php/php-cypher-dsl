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

use WikibaseSolutions\CypherDSL\Assignment;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * This class represents a MATCH clause.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/match/
 */
class MatchClause extends Clause
{
    use ErrorTrait;

    /**
     * @var StructuralType[]|Assignment[] List of patterns
     */
    private array $patterns = [];

    /**
     * Add a pattern to the match clause.
     *
     * @param StructuralType|Assignment $pattern
     * @return MatchClause
     */
    public function addPattern( $pattern ): self
    {
        $this->assertClass('pattern', [StructuralType::class, Assignment::class], $pattern);

        $this->patterns[] = $pattern;

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function getClause(): string
    {
        return "MATCH";
    }

    /**
     * @inheritDoc
     */
    protected function getSubject(): string
    {
        return implode(
            ", ",
            array_map(fn( $pattern ): string => $pattern->toQuery(), $this->patterns)
        );
    }
}
