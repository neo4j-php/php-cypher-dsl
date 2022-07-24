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

use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * This class represents the application of a unary operator, such as "-" and "NOT".
 */
abstract class UnaryOperator implements QueryConvertible
{
	/**
     * @var bool Whether to insert parentheses around the expression
     */
    private bool $insertParentheses;

    /**
     * @var AnyType The expression
     */
    private AnyType $expression;

    /**
     * UnaryOperator constructor.
     *
     * @param AnyType $expression The expression
     * @param bool $insertParentheses Whether to insert parentheses around the expression
     */
    public function __construct(AnyType $expression, bool $insertParentheses = true)
    {
        $this->expression = $expression;
        $this->insertParentheses = $insertParentheses;
    }

	/**
	 * Returns whether this is a postfix operator or not.
	 *
	 * @return bool
	 */
	public function isPostfix(): bool
	{
		return false;
	}

	/**
	 * Returns the expression to negate.
	 *
	 * @return AnyType
	 */
	public function getExpression(): AnyType
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
		$format = $this->insertParentheses ? "(%s %s)" : "%s %s";
		$expression = $this->expression->toQuery();
		$operator = $this->getOperator();

        return $this->isPostfix() ?
			sprintf( $format, $expression, $operator ) :
			sprintf( $format, $operator, $expression );
    }

    /**
     * Returns the operator. For instance, this function would return "-" for the minus operator.
     *
     * @return string
     */
    abstract protected function getOperator(): string;
}
