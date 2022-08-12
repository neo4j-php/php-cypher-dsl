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

namespace WikibaseSolutions\CypherDSL\Types\PropertyTypes;

use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\PropertyTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;

/**
 * Represents any property type in Cypher.
 *
 * Property types are types that:
 *
 * - can be returned from Cypher queries
 * - can be used as parameters
 * - can be stored as properties
 * - can be constructed with Cypher literals
 *
 * The property types are:
 *
 * - number, which has subtypes integer and float
 * - string
 * - boolean
 * - point
 * - temporal, which has subtypes date, time, localtime, datetime, localdatetime and duration
 *
 * Homogeneous lists of simple types can also be stored as properties, although lists in
 * general cannot.
 *
 * @see PropertyTypeTrait for a default implementation
 * @note This interface should not be implemented by any class directly.
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#property-types
 */
interface PropertyType extends AnyType
{
    /**
     * Checks whether the element exists in the given list.
     *
     * @param ListType|array $right
     * @return In
     */
    public function in($right): In;
}
