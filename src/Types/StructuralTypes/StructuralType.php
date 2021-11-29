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

namespace WikibaseSolutions\CypherDSL\Types\StructuralTypes;

use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * Represents any structural type in Cypher.
 *
 * Structural types are types that:
 *
 * - can be returned from Cypher queries
 * - cannot be used as parameters
 * - cannot be stored as properties
 * - cannot be constructed with Cypher literals
 *
 * The structural types are:
 *
 * - node
 * - relationship
 * - path
 *
 * @note This interface should not be implemented by any class directly.
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types
 */
interface StructuralType extends AnyType
{
    /**
     * Creates a new relationship from this node to the given pattern.
     *
     * @param StructuralType $pattern
     * @return Path
     */
    public function relationshipTo(StructuralType $pattern): Path;

    /**
     * Creates a new relationship from the given pattern to this node.
     *
     * @param StructuralType $pattern
     * @return Path
     */
    public function relationshipFrom(StructuralType $pattern): Path;

    /**
     * Creates a new unidirectional relationship between this node and the given pattern.
     *
     * @param StructuralType $pattern
     * @return Path
     */
    public function relationshipUni(StructuralType $pattern): Path;
}