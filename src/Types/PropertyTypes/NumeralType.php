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

use WikibaseSolutions\CypherDSL\Addition;
use WikibaseSolutions\CypherDSL\Division;
use WikibaseSolutions\CypherDSL\Exponentiation;
use WikibaseSolutions\CypherDSL\Minus;
use WikibaseSolutions\CypherDSL\Modulo;
use WikibaseSolutions\CypherDSL\Multiplication;
use WikibaseSolutions\CypherDSL\Subtraction;

/**
 * Represents any numeral (integer, float, double).
 */
interface NumeralType extends AliasablePropertyType, ComparablePropertyType
{
    /**
     * Add this expression to the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Addition
     */
    public function plus(self $right, bool $insertParentheses = true): Addition;

    /**
     * Divide this expression by the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Division
     */
    public function divide(self $right, bool $insertParentheses = true): Division;

    /**
     * Perform an exponentiation with the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Exponentiation
     */
    public function exponentiate(self $right, bool $insertParentheses = true): Exponentiation;

    /**
     * Perform the modulo operation with the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Modulo
     */
    public function mod(self $right, bool $insertParentheses = true): Modulo;

    /**
     * Perform a multiplication with the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Multiplication
     */
    public function times(self $right, bool $insertParentheses = true): Multiplication;

    /**
     * Subtract the given expression from this expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Subtraction
     */
    public function minus(self $right, bool $insertParentheses = true): Subtraction;

    /**
     * Negate this expression (negate the numeral using "0").
     *
     * @return Minus
     */
    public function negate(): Minus;
}
