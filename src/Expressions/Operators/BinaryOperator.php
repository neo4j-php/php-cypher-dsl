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
 * This class represents the application of a binary operator, such as "+", "/" and "*".
 */
abstract class BinaryOperator extends Operator
{
    /**
     * @var AnyType The left-hand of the expression
     */
    private AnyType $left;

    /**
     * @var AnyType The right-hand of the expression
     */
    private AnyType $right;

    /**
     * BinaryOperator constructor.
     *
     * @param AnyType $left The left-hand of the expression
     * @param AnyType $right The right-hand of the expression
     * @param bool $insertParentheses Whether to insert parentheses around the expression
     */
    public function __construct(AnyType $left, AnyType $right, bool $insertParentheses = true)
    {
		parent::__construct($insertParentheses);
        $this->left = $left;
        $this->right = $right;
    }

	/**
	 * Gets the left-hand of the expression.
	 *
	 * @return AnyType
	 */
	public function getLeft(): AnyType
	{
		return $this->left;
	}

	/**
	 * Gets the right-hand of the expression.
	 *
	 * @return AnyType
	 */
	public function getRight(): AnyType
	{
		return $this->right;
	}

    /**
     * @inheritDoc
     */
    public function toInner(): string
    {
		return sprintf("%s %s %s", $this->left->toQuery(), $this->getOperator(), $this->right->toQuery());
	}
}
