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

namespace WikibaseSolutions\CypherDSL\Expressions\Operators;

use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * This class represents the application of a unary operator, such as "-" and "NOT".
 */
abstract class UnaryOperator extends Operator
{
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
		parent::__construct($insertParentheses);

        $this->expression = $expression;
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
     * @inheritDoc
     */
    public function toInner(): string
    {
		$expression = $this->expression->toQuery();
		$operator = $this->getOperator();

        return $this->isPostfix() ?
			sprintf( "%s %s", $expression, $operator ) :
			sprintf( "%s %s", $operator, $expression );
    }
}
