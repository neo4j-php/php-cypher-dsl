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

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Traits\PatternTraits\PatternTrait;

/**
 * This class represents a pattern. A pattern can be:
 *
 * - a node
 * - a path (alternating sequence of nodes and relationships)
 * - a relationship
 *
 * A pattern is not an expression, but rather a syntactic construct used for pattern matching on the graph
 * database. It therefore does not have any type, and thus it cannot be used in an expression. Instead, the variable
 * to which this pattern is assigned should be used. This library makes this easier by generating a variable for a
 * pattern when necessary and by casting Pattern instances to their associated variable when used in an expression.
 *
 * @note This interface should not be implemented by any class directly. Use any of the sub-interfaces instead:
 *
 * 	- PropertiedPattern: for patterns that can contain properties
 * 	- RelatablePattern: for patterns that can be related to eachother using a Relationship
 * 	- MatchablePattern: for patterns that can be matched in a MATCH clause
 *
 * @see PatternTrait for a default implementation
 */
interface Pattern extends QueryConvertible
{
    /**
     * Explicitly assign a named variable to this pattern.
     *
     * @param Variable|string $variable
     * @return $this
     */
    public function withVariable($variable): Pattern;

    /**
     * Returns the variable of this pattern. This function generates a variable if none has been set.
     *
     * @return Variable
     */
    public function getVariable(): Variable;
}
