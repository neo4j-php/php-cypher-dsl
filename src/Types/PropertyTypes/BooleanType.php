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

use WikibaseSolutions\CypherDSL\Expressions\Operators\Conjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveDisjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Negation;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;

/**
 * Represents the leaf type "boolean".
 *
 * @see BooleanTypeTrait for a default implementation
 */
interface BooleanType extends PropertyType
{
    /**
     * Create a conjunction between this expression and the given expression.
     *
     * @param BooleanType|bool $right
     * @param bool $insertParentheses
     * @return Conjunction
     */
    public function and($right, bool $insertParentheses = true): Conjunction;

    /**
     * Create a disjunction between this expression and the given expression.
     *
     * @param BooleanType|bool $right
     * @param bool $insertParentheses
     * @return Disjunction
     */
    public function or($right, bool $insertParentheses = true): Disjunction;

    /**
     * Perform an XOR with the given expression.
     *
     * @param BooleanType|bool $right
     * @param bool $insertParentheses
     * @return ExclusiveDisjunction
     */
    public function xor($right, bool $insertParentheses = true): ExclusiveDisjunction;

    /**
     * Negate this expression (using the NOT operator).
     *
     * @return Negation
     */
    public function not(): Negation;
}
