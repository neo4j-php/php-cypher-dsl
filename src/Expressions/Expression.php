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
	 * @return Addition
	 */
	public function plus(Expression $right): Addition
	{
		return new Addition($this, $right);
	}

	/**
	 * Create a conjunction between this expression and the given expression.
	 *
	 * @param Expression $right
	 * @return AndOperator
	 */
	public function and(Expression $right): AndOperator
	{
		return new AndOperator($this, $right);
	}

	/**
	 * Check whether this expression the given expression.
	 *
	 * @param Expression $right
	 * @return Contains
	 */
	public function contains(Expression $right): Contains
	{
		return new Contains($this, $right);
	}

	/**
	 * Divide this expression by the given expression.
	 *
	 * @param Expression $right
	 * @return Division
	 */
	public function divide(Expression $right): Division
	{
		return new Division($this, $right);
	}

	/**
	 * Perform a suffix string search with the given expression.
	 *
	 * @param Expression $right
	 * @return EndsWith
	 */
	public function endsWith(Expression $right): EndsWith
	{
		return new EndsWith($this, $right);
	}

	/**
	 * Perform an equality check or an assignment with the given expression.
	 *
	 * @param Expression $right
	 * @return Equality
	 */
	public function equals(Expression $right): Equality
	{
		return new Equality($this, $right);
	}

	/**
	 * Perform an exponentiation with the given expression.
	 *
	 * @param Expression $right
	 * @return Exponentiation
	 */
	public function exponentiate(Expression $right): Exponentiation
	{
		return new Exponentiation($this, $right);
	}

	/**
	 * Perform a greater than comparison against the given expression.
	 *
	 * @param Expression $right
	 * @return GreaterThan
	 */
	public function gt(Expression $right): GreaterThan
	{
		return new GreaterThan($this, $right);
	}

	/**
	 * Perform a greater than or equal comparison against the given expression.
	 *
	 * @param Expression $right
	 * @return GreaterThanOrEqual
	 */
	public function gte(Expression $right): GreaterThanOrEqual
	{
		return new GreaterThanOrEqual($this, $right);
	}

	/**
	 * Perform a inequality comparison against the given expression.
	 *
	 * @param Expression $right
	 * @return Inequality
	 */
	public function notEquals(Expression $right): Inequality
	{
		return new Inequality($this, $right);
	}

	/**
	 * Perform a less than comparison against the given expression.
	 *
	 * @param Expression $right
	 * @return LessThan
	 */
	public function lt(Expression $right): LessThan
	{
		return new LessThan($this, $right);
	}

	/**
	 * Perform a less than or equal comparison against the given expression.
	 *
	 * @param Expression $right
	 * @return LessThanOrEqual
	 */
	public function lte(Expression $right): LessThanOrEqual
	{
		return new LessThanOrEqual($this, $right);
	}

	/**
	 * Perform the modulo operation with the given expression.
	 *
	 * @param Expression $right
	 * @return Modulo
	 */
	public function mod(Expression $right): Modulo
	{
		return new Modulo($this, $right);
	}

	/**
	 * Perform a multiplication with the given expression.
	 *
	 * @param Expression $right
	 * @return Multiplication
	 */
	public function times(Expression $right): Multiplication
	{
		return new Multiplication($this, $right);
	}

	/**
	 * Create a disjunction between this expression and the given expression.
	 *
	 * @param Expression $right
	 * @return OrOperator
	 */
	public function or(Expression $right): OrOperator
	{
		return new OrOperator($this, $right);
	}

	/**
	 * Perform a plus-equals (+=) assignment with the given expression.
	 *
	 * @param Expression $right
	 * @return PropertyMutation
	 */
	public function plusEquals(Expression $right): PropertyMutation
	{
		return new PropertyMutation($this, $right);
	}

	/**
	 * Perform a prefix string search with the given expression.
	 *
	 * @param Expression $right
	 * @return StartsWith
	 */
	public function startsWith(Expression $right): StartsWith
	{
		return new StartsWith($this, $right);
	}

	/**
	 * Subtract the given expression from this expression.
	 *
	 * @param Expression $right
	 * @return Subtraction
	 */
	public function minus(Expression $right): Subtraction
	{
		return new Subtraction($this, $right);
	}

	/**
	 * Perform an XOR with the given expression.
	 *
	 * @param Expression $right
	 * @return XorOperator
	 */
	public function xor(Expression $right): XorOperator
	{
		return new XorOperator($this, $right);
	}

	/**
	 * Add a label to this expression. For instance, if this expression is the variable "foo",
	 * a function call like $expression->labeled("bar") would yield "foo:bar".
	 *
	 * @param string $label
	 * @return Label
	 */
	public function labeled(string $label): Label
	{
		return new Label($this, $label);
	}

	/**
	 * Returns the property of the given name for this expression. For instance, if this expression is the
	 * variable "foo", a function call like $expression->property("bar") would yield "foo.bar".
	 *
	 * @param string $property
	 * @return Property
	 */
	public function property(string $property): Property
	{
		return new Property($this, $property);
	}

	/**
	 * Negate this expression.
	 *
	 * @return Minus
	 */
	public function negate(): Minus
	{
		return new Minus($this);
	}

    /**
     * Converts the expression into a query.
     *
     * @return string
     */
    abstract public function toQuery(): string;
}