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

use WikibaseSolutions\CypherDSL\Equality;
use WikibaseSolutions\CypherDSL\In;
use WikibaseSolutions\CypherDSL\Inequality;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * This trait should be used by any expression that returns a property type.
 *
 * @note This trait should not be used by any class directly.
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#property-types
 */
trait PropertyTypeTrait
{
    /**
     * Perform an equality check or an assignment with the given expression.
     *
     * @param PropertyType $right
     * @return Equality
     */
    public function equals(PropertyType $right): Equality
    {
        return new Equality($this, $right);
    }

    /**
     * Perform an inequality comparison against the given expression.
     *
     * @param PropertyType $right
     * @return Inequality
     */
    public function notEquals(PropertyType $right): Inequality
    {
        return new Inequality($this, $right);
    }

    /**
     * Checks whether the element exists in the given list.
     *
     * @param ListType $right
     * @return In
     */
    public function in(ListType $right): In
    {
        return new In($this, $right);
    }
}