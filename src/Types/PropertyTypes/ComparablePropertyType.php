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

use WikibaseSolutions\CypherDSL\GreaterThan;
use WikibaseSolutions\CypherDSL\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\LessThan;
use WikibaseSolutions\CypherDSL\LessThanOrEqual;

/**
 * Represents any type that can be compared with operators such as <=, >=, < and >.
 */
interface ComparablePropertyType extends PropertyType
{
    /**
     * Perform a greater than comparison against the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return GreaterThan
     */
    public function gt(ComparablePropertyType $right, bool $insertParentheses = true): GreaterThan;

    /**
     * Perform a greater than or equal comparison against the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return GreaterThanOrEqual
     */
    public function gte(ComparablePropertyType $right, bool $insertParentheses = true): GreaterThanOrEqual;

    /**
     * Perform a less than comparison against the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return LessThan
     */
    public function lt(ComparablePropertyType $right, bool $insertParentheses = true): LessThan;

    /**
     * Perform a less than or equal comparison against the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return LessThanOrEqual
     */
    public function lte(ComparablePropertyType $right, bool $insertParentheses = true): LessThanOrEqual;
}
