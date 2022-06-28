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

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits;

use WikibaseSolutions\CypherDSL\Equality;
use WikibaseSolutions\CypherDSL\In;
use WikibaseSolutions\CypherDSL\Inequality;
use WikibaseSolutions\CypherDSL\IsNotNull;
use WikibaseSolutions\CypherDSL\IsNull;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * This trait provides a default implementation to satisfy the "PropertyType" interface.
 *
 * @see PropertyType
 */
trait PropertyTypeTrait
{
    /**
     * Perform an equality check or an assignment with the given expression.
     *
     * @param PropertyType $right
     * @param bool $insertParentheses
     * @return Equality
     */
    public function equals(PropertyType $right, bool $insertParentheses = true): Equality
    {
        return new Equality($this, $right, $insertParentheses);
    }

    /**
     * Perform an inequality comparison against the given expression.
     *
     * @param PropertyType $right
     * @param bool $insertParentheses
     * @return Inequality
     */
    public function notEquals(PropertyType $right, bool $insertParentheses = true): Inequality
    {
        return new Inequality($this, $right, $insertParentheses);
    }

    /**
     * Checks whether the element exists in the given list.
     *
     * @param ListType $right
     * @param bool $insertParentheses
     * @return In
     */
    public function in(ListType $right, bool $insertParentheses = true): In
    {
        return new In($this, $right, $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function isNull(bool $insertParentheses = true): IsNull
    {
        return new IsNull($this, $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function isNotNull(bool $insertParentheses = true): IsNotNull
    {
        return new IsNotNull($this, $insertParentheses);
    }
}
