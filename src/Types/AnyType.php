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

namespace WikibaseSolutions\CypherDSL\Types;

use WikibaseSolutions\CypherDSL\Expressions\Operators\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Inequality;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNotNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\IsNull;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThan;
use WikibaseSolutions\CypherDSL\Expressions\Operators\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Pattern;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Syntax\Alias;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\AnyTypeTrait;

/**
 * Represents any type in Cypher.
 *
 * @see AnyTypeTrait for a default implementation
 * @note This interface should not be implemented by any concrete class directly.
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/
 */
interface AnyType extends QueryConvertible
{
    /**
     * Creates an alias of the current expression.
     *
     * @param Variable|string $right
     * @return Alias
     */
    public function alias($right): Alias;

    /**
     * Perform an equality check with the given expression.
     *
     * @param AnyType|Pattern|int|float|string|bool|array $right
     * @param bool $insertParentheses
     * @return Equality
     */
    public function equals($right, bool $insertParentheses = true): Equality;

    /**
     * Perform an inequality comparison against the given expression.
     *
     * @param AnyType|Pattern|int|float|string|bool|array $right
     * @param bool $insertParentheses
     * @return Inequality
     */
    public function notEquals($right, bool $insertParentheses = true): Inequality;

    /**
     * Perform a greater than comparison against the given expression.
     *
     * @param AnyType|Pattern|int|float|string|bool|array $right
     * @param bool $insertParentheses
     * @return GreaterThan
     */
    public function gt($right, bool $insertParentheses = true): GreaterThan;

    /**
     * Perform a greater than or equal comparison against the given expression.
     *
     * @param AnyType|Pattern|int|float|string|bool|array $right
     * @param bool $insertParentheses
     * @return GreaterThanOrEqual
     */
    public function gte($right, bool $insertParentheses = true): GreaterThanOrEqual;

    /**
     * Perform a less than comparison against the given expression.
     *
     * @param AnyType|Pattern|int|float|string|bool|array $right
     * @param bool $insertParentheses
     * @return LessThan
     */
    public function lt($right, bool $insertParentheses = true): LessThan;

    /**
     * Perform a less than or equal comparison against the given expression.
     *
     * @param AnyType|Pattern|int|float|string|bool|array $right
     * @param bool $insertParentheses
     * @return LessThanOrEqual
     */
    public function lte($right, bool $insertParentheses = true): LessThanOrEqual;

    /**
     * Checks whether the element is null.
     *
     * @param bool $insertParentheses
     * @return IsNull
     */
    public function isNull(bool $insertParentheses = true): IsNull;

    /**
     * Checks whether the element is not null.
     *
     * @param bool $insertParentheses
     * @return IsNotNull
     */
    public function isNotNull(bool $insertParentheses = true): IsNotNull;
}
