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

use WikibaseSolutions\CypherDSL\QueryConvertable;

/**
 * Represents any valid expression in Cypher. An expression can be:
 *
 * - A decimal literal;
 * - A decimal literal in scientific notation;
 * - A hexadecimal integer literal;
 * - An octal integer literal;
 * - A string literal;
 * - A boolean literal;
 * - A variable;
 * - A property;
 * - A dynamic property;
 * - A parameter;
 * - A list of expressions;
 * - A function call;
 * - An aggregate function;
 * - A path-pattern;
 * - An operator application;
 * - A predicate expression;
 * - An existential sub-query;
 * - A regular expression;
 * - A case-sensitive string;
 * - A CASE expression.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/expressions/
 */
abstract class Expression implements QueryConvertable
{
	/**
	 * Add this expression to the given expression.
	 *
	 * @param Expression $right
	 * @return Expression
	 */
	public function plus(Expression $right): Expression
	{
		return new Addition($this, $right);
	}

	/**
	 * Create a conjunction between this expression and the given expression.
	 *
	 * @param Expression $right
	 * @return Expression
	 */
	public function and(Expression $right): Expression
	{
		return new AndOperator($this, $right);
	}

	/**
	 * Check whether this expression the given expression.
	 *
	 * @param Expression $right
	 * @return Expression
	 */
	public function contains(Expression $right): Expression
	{
		return new Contains($this, $right);
	}

	/**
	 * Divide this expression by the given expression.
	 *
	 * @param Expression $right
	 * @return Expression
	 */
	public function divideBy(Expression $right): Expression
	{
		return new Division($this, $right);
	}

	public function endsWith(Expression $right): Expression
	{
		// TODO
	}

	public function equals(Expression $right): Expression
	{
		// TODO
	}

	public function exponentiate(Expression $right): Expression
	{
		// TODO
	}

	public function gt(Expression $right): Expression
	{
		// TODO
	}

	public function gte(Expression $right): Expression
	{
		// TODO
	}

	public function notEquals(Expression $right): Expression
	{
		// TODO
	}

	public function lt(Expression $right): Expression
	{
		// TODO
	}

	public function lte(Expression $right): Expression
	{
		// TODO
	}

	public function mod(Expression $right): Expression
	{
		// TODO
	}

	public function times(Expression $expression): Expression
	{
		// TODO
	}

	public function or(Expression $expression): Expression
	{
		// TODO
	}

	public function propertyReplacement(Expression $expression): Expression
	{
		// TODO
	}

	public function startsWith(Expression $expression): Expression
	{
		// TODO
	}

	public function minus(Expression $expression): Expression
	{
		// TODO
	}

	public function xor(Expression $expression): Expression
	{
		// TODO
	}

    /**
     * Converts the expression into a query.
     *
     * @return string
     */
    abstract public function toQuery(): string;
}