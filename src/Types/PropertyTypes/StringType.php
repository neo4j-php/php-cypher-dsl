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

use WikibaseSolutions\CypherDSL\Expressions\Contains;
use WikibaseSolutions\CypherDSL\Expressions\EndsWith;
use WikibaseSolutions\CypherDSL\Expressions\Regex;
use WikibaseSolutions\CypherDSL\Expressions\StartsWith;

/**
 * Represents the leaf type "string".
 */
interface StringType extends AliasablePropertyType, ComparablePropertyType
{
    /**
     * Check whether this expression the given expression.
     *
     * @param StringType $right
     * @param bool $insertParentheses
     * @return Contains
     */
    public function contains(self $right, bool $insertParentheses = true): Contains;

    /**
     * Perform a suffix string search with the given expression.
     *
     * @param StringType $right
     * @param bool $insertParentheses
     * @return EndsWith
     */
    public function endsWith(self $right, bool $insertParentheses = true): EndsWith;

    /**
     * Perform a prefix string search with the given expression.
     *
     * @param StringType $right
     * @param bool $insertParentheses
     * @return StartsWith
     */
    public function startsWith(self $right, bool $insertParentheses = true): StartsWith;

    /**
     * Perform a regex comparison with the given expression.
     *
     * @param StringType $right
     * @param bool $insertParentheses
     * @return Regex
     */
    public function regex(self $right, bool $insertParentheses = true): Regex;
}
