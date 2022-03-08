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

namespace WikibaseSolutions\CypherDSL\Traits;

use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * This trait should be used by any expression that returns a structural type.
 *
 * @note This trait should not be used by any class directly.
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types
 */
trait StructuralTypeTrait
{
    /**
     * Creates a new relationship from this node to the given pattern.
     *
     * @param StructuralType $pattern
     * @return Relationship
     */
    public function relationshipTo(StructuralType $pattern): Relationship
    {
        return new Relationship($this, $pattern, Relationship::DIR_RIGHT);
    }

    /**
     * Creates a new relationship from the given pattern to this node.
     *
     * @param StructuralType $pattern
     * @return Relationship
     */
    public function relationshipFrom(StructuralType $pattern): Relationship
    {
        return new Relationship($this, $pattern, Relationship::DIR_LEFT);
    }

    /**
     * Creates a new unidirectional relationship between this node and the given pattern.
     *
     * @param StructuralType $pattern
     * @return Relationship
     */
    public function relationshipUni(StructuralType $pattern): Relationship
    {
        return new Relationship($this, $pattern, Relationship::DIR_UNI);
    }
}
