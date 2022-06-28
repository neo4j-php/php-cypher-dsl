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

use WikibaseSolutions\CypherDSL\GreaterThan;
use WikibaseSolutions\CypherDSL\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\LessThan;
use WikibaseSolutions\CypherDSL\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\ComparablePropertyType;

/**
 * This trait provides a default implementation to satisfy the "ComparablePropertyType" interface.
 *
 * @see ComparablePropertyType
 */
trait ComparablePropertyTypeTrait
{
    use PropertyTypeTrait;

    /**
     * Perform a greater than comparison against the given expression.
     *
     * @param ComparablePropertyType $right
     * @param bool $insertParentheses
     * @return GreaterThan
     */
    public function gt(ComparablePropertyType $right, bool $insertParentheses = true): GreaterThan
    {
        return new GreaterThan($this, $right, $insertParentheses);
    }

    /**
     * Perform a greater than or equal comparison against the given expression.
     *
     * @param ComparablePropertyType $right
     * @param bool $insertParentheses
     * @return GreaterThanOrEqual
     */
    public function gte(ComparablePropertyType $right, bool $insertParentheses = true): GreaterThanOrEqual
    {
        return new GreaterThanOrEqual($this, $right, $insertParentheses);
    }

    /**
     * Perform a less than comparison against the given expression.
     *
     * @param ComparablePropertyType $right
     * @param bool $insertParentheses
     * @return LessThan
     */
    public function lt(ComparablePropertyType $right, bool $insertParentheses = true): LessThan
    {
        return new LessThan($this, $right, $insertParentheses);
    }

    /**
     * Perform a less than or equal comparison against the given expression.
     *
     * @param ComparablePropertyType $right
     * @param bool $insertParentheses
     * @return LessThanOrEqual
     */
    public function lte(ComparablePropertyType $right, bool $insertParentheses = true): LessThanOrEqual
    {
        return new LessThanOrEqual($this, $right, $insertParentheses);
    }
}
