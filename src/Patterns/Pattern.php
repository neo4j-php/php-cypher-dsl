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
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\HasVariableTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * This class represents a pattern. A pattern can be:
 *
 * - a node
 * - a path (alternating sequence of nodes and relationships)
 *
 * A pattern is not an expression, but rather a syntactic construct used for pattern matching on the graph
 * database. It therefore does not have any type, and thus it cannot be used in an expression. Instead, the variable
 * to which this pattern is assigned should be used. This library makes this easier by generating a variable for a
 * pattern when necessary and by casting Pattern instances to their associated variable when used in an expression.
 *
 * @see Relationship for the class implementing a relationship
 */
abstract class Pattern implements QueryConvertible
{
    use CastTrait;
    use ErrorTrait;
    use HasVariableTrait;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using the given relationship
     * pattern.
     *
     * @param Relationship $relationship The relationship to use
     * @param Pattern $pattern The relatable pattern to attach to this pattern
     *
     * @return Path
     */
    abstract public function relationship(Relationship $relationship, Pattern $pattern): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a right (-->)
     * relationship.
     *
     * @param Pattern $pattern The pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param MapType|array|null $properties The properties to attach to the relationship
     * @param Variable|string|null $name The name fo the relationship
     *
     * @return Path
     */
    abstract public function relationshipTo(Pattern $pattern, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a left (<--)
     * relationship.
     *
     * @param Pattern $pattern The pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param MapType|array|null $properties The properties to attach to the relationship
     * @param Variable|string|null $name The name fo the relationship
     *
     * @return Path
     */
    abstract public function relationshipFrom(Pattern $pattern, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a unidirectional
     * (--/<-->) relationship.
     *
     * @param Pattern $pattern The pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param MapType|array|null $properties The properties to attach to the relationship
     * @param Variable|string|null $name The name fo the relationship
     *
     * @return Path
     */
    abstract public function relationshipUni(Pattern $pattern, ?string $type = null, $properties = null, $name = null): Path;
}
