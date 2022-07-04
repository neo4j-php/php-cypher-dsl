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

namespace WikibaseSolutions\CypherDSL\Expressions;

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\NumeralTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\NumeralType;

/**
 * This class represents an application of the unary minus operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#syntax-using-the-unary-minus-operator
 */
class Minus implements NumeralType
{
    use NumeralTypeTrait;

    /**
     * @var NumeralType The expression to negate
     */
    private NumeralType $expression;

	/**
	 * @var bool Whether to insert parentheses around the expression
	 */
	private bool $insertParentheses;

    /**
     * Minus constructor.
     *
     * @param NumeralType $expression The expression to negate
	 * @param bool $insertParentheses Whether to insert parentheses around the expression
     */
    public function __construct(NumeralType $expression, bool $insertParentheses = false)
    {
        $this->expression = $expression;
		$this->insertParentheses = $insertParentheses;
    }

    /**
     * Returns the expression to negate.
     *
     * @return NumeralType
     */
    public function getExpression(): NumeralType
    {
        return $this->expression;
    }

	/**
	 * Returns whether the operator inserts parenthesis.
	 *
	 * @return bool
	 */
	public function insertsParentheses(): bool
	{
		return $this->insertParentheses;
	}

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf($this->insertParentheses ? "(-%s)" : "-%s", $this->expression->toQuery());
    }
}
