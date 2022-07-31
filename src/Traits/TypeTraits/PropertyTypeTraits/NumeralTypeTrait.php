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

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Operators\Addition;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Division;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Exponentiation;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ModuloDivision;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Multiplication;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Subtraction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryMinus;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * This trait provides a default implementation to satisfy the "NumeralType" interface.
 *
 * @implements NumeralType
 */
trait NumeralTypeTrait
{
	use PropertyTypeTrait;

	/**
	 * @inheritDoc
	 */
    public function plus($right, bool $insertParentheses = true): Addition
    {
        return new Addition($this, self::toNumeralType($right), $insertParentheses);
    }

	/**
	 * @inheritDoc
	 */
    public function divide($right, bool $insertParentheses = true): Division
    {
        return new Division($this, self::toNumeralType($right), $insertParentheses);
    }

	/**
	 * @inheritDoc
	 */
    public function exponentiate($right, bool $insertParentheses = true): Exponentiation
    {
        return new Exponentiation($this, self::toNumeralType($right), $insertParentheses);
    }

	/**
	 * @inheritDoc
	 */
    public function mod($right, bool $insertParentheses = true): ModuloDivision
    {
        return new ModuloDivision($this, self::toNumeralType($right), $insertParentheses);
    }

	/**
	 * @inheritDoc
	 */
    public function times($right, bool $insertParentheses = true): Multiplication
    {
        return new Multiplication($this, self::toNumeralType($right), $insertParentheses);
    }

	/**
	 * @inheritDoc
	 */
    public function minus($right, bool $insertParentheses = true): Subtraction
    {
        return new Subtraction($this, self::toNumeralType($right), $insertParentheses);
    }

	/**
	 * @inheritDoc
	 */
    public function negate(): UnaryMinus
    {
        return new UnaryMinus($this);
    }
}
