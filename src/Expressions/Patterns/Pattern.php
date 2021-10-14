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

namespace WikibaseSolutions\CypherDSL\Expressions\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\Expression;
use WikibaseSolutions\CypherDSL\Expressions\Types\PatternType;

/**
 * Marker interface that represents a pattern. A pattern describes the shape of the data you are looking for.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/
 */
abstract class Pattern extends Expression
{
    /**
     * Creates a new relationship from this node to the given pattern.
     *
     * @param  PatternType $pattern
     * @return Relationship
     */
    public function relationshipTo(PatternType $pattern): Relationship
    {
        return new Relationship($this, $pattern, Relationship::DIR_RIGHT);
    }

    /**
     * Creates a new relationship from the given pattern to this node.
     *
     * @param  PatternType $pattern
     * @return Relationship
     */
    public function relationshipFrom(PatternType $pattern): Relationship
    {
        return new Relationship($this, $pattern, Relationship::DIR_LEFT);
    }

    /**
     * Creates a new unidirectional relationship between this node and the given pattern.
     *
     * @param  PatternType $pattern
     * @return Relationship
     */
    public function relationshipUni(PatternType $pattern): Relationship
    {
        return new Relationship($this, $pattern, Relationship::DIR_UNI);
    }

    /**
     * Converts the pattern into a query.
     *
     * @return string
     */
    abstract public function toQuery(): string;
}
