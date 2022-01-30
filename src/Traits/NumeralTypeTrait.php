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
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * This trait should be used by any expression that returns a numeral.
 */
trait NumeralTypeTrait
{
    use PropertyTypeTrait;

    /**
     * Add this expression to the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Addition
     */
    public function plus(NumeralType $right, bool $insertParentheses = true): Addition
    {
        return new Addition($this, $right, $insertParentheses);
    }

    /**
     * Divide this expression by the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Division
     */
    public function divide(NumeralType $right, bool $insertParentheses = true): Division
    {
        return new Division($this, $right, $insertParentheses);
    }

    /**
     * Perform an exponentiation with the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Exponentiation
     */
    public function exponentiate(NumeralType $right, bool $insertParentheses = true): Exponentiation
    {
        return new Exponentiation($this, $right, $insertParentheses);
    }

    /**
     * Perform a greater than comparison against the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return GreaterThan
     */
    public function gt(NumeralType $right, bool $insertParentheses = true): GreaterThan
    {
        return new GreaterThan($this, $right, $insertParentheses);
    }

    /**
     * Perform a greater than or equal comparison against the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return GreaterThanOrEqual
     */
    public function gte(NumeralType $right, bool $insertParentheses = true): GreaterThanOrEqual
    {
        return new GreaterThanOrEqual($this, $right, $insertParentheses);
    }

    /**
     * Perform a less than comparison against the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return LessThan
     */
    public function lt(NumeralType $right, bool $insertParentheses = true): LessThan
    {
        return new LessThan($this, $right, $insertParentheses);
    }

    /**
     * Perform a less than or equal comparison against the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return LessThanOrEqual
     */
    public function lte(NumeralType $right, bool $insertParentheses = true): LessThanOrEqual
    {
        return new LessThanOrEqual($this, $right, $insertParentheses);
    }

    /**
     * Perform the modulo operation with the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Modulo
     */
    public function mod(NumeralType $right, bool $insertParentheses = true): Modulo
    {
        return new Modulo($this, $right, $insertParentheses);
    }

    /**
     * Perform a multiplication with the given expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Multiplication
     */
    public function times(NumeralType $right, bool $insertParentheses = true): Multiplication
    {
        return new Multiplication($this, $right, $insertParentheses);
    }

    /**
     * Subtract the given expression from this expression.
     *
     * @param NumeralType $right
     * @param bool $insertParentheses
     * @return Subtraction
     */
    public function minus(NumeralType $right, bool $insertParentheses = true): Subtraction
    {
        return new Subtraction($this, $right, $insertParentheses);
    }

    /**
     * Negate this expression (negate the numeral using "-").
     *
     * @return Minus
     */
    public function negate(): Minus
    {
        return new Minus($this);
    }
}