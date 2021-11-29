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
use WikibaseSolutions\CypherDSL\GreaterThan;
use WikibaseSolutions\CypherDSL\GreaterThanOrEqual;
use WikibaseSolutions\CypherDSL\LessThan;
use WikibaseSolutions\CypherDSL\LessThanOrEqual;
use WikibaseSolutions\CypherDSL\Minus;
use WikibaseSolutions\CypherDSL\Modulo;
use WikibaseSolutions\CypherDSL\Multiplication;
use WikibaseSolutions\CypherDSL\Subtraction;

/**
 * Represents any numeral (integer, float, double).
 */
interface NumeralType extends PropertyType
{
    /**
     * Add this expression to the given expression.
     *
     * @param NumeralType $right
     * @return Addition
     */
    public function plus(NumeralType $right): Addition;

    /**
     * Divide this expression by the given expression.
     *
     * @param NumeralType $right
     * @return Division
     */
    public function divide(NumeralType $right): Division;

    /**
     * Perform an exponentiation with the given expression.
     *
     * @param NumeralType $right
     * @return Exponentiation
     */
    public function exponentiate(NumeralType $right): Exponentiation;

    /**
     * Perform a greater than comparison against the given expression.
     *
     * @param NumeralType $right
     * @return GreaterThan
     */
    public function gt(NumeralType $right): GreaterThan;

    /**
     * Perform a greater than or equal comparison against the given expression.
     *
     * @param NumeralType $right
     * @return GreaterThanOrEqual
     */
    public function gte(NumeralType $right): GreaterThanOrEqual;

    /**
     * Perform a less than comparison against the given expression.
     *
     * @param NumeralType $right
     * @return LessThan
     */
    public function lt(NumeralType $right): LessThan;

    /**
     * Perform a less than or equal comparison against the given expression.
     *
     * @param NumeralType $right
     * @return LessThanOrEqual
     */
    public function lte(NumeralType $right): LessThanOrEqual;

    /**
     * Perform the modulo operation with the given expression.
     *
     * @param NumeralType $right
     * @return Modulo
     */
    public function mod(NumeralType $right): Modulo;

    /**
     * Perform a multiplication with the given expression.
     *
     * @param NumeralType $right
     * @return Multiplication
     */
    public function times(NumeralType $right): Multiplication;

    /**
     * Subtract the given expression from this expression.
     *
     * @param NumeralType $right
     * @return Subtraction
     */
    public function minus(NumeralType $right): Subtraction;

    /**
     * Negate this expression.
     *
     * @return Minus
     */
    public function negate(): Minus;
}