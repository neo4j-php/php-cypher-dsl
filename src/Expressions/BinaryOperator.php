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

/**
 * This class represents the application of a binary operator, such as "+", "/" and "*".
 */
abstract class BinaryOperator extends Expression
{
    /**
     * @var Expression The left-hand of the expression
     */
    private Expression $left;

    /**
     * @var Expression The right-hand of the expression
     */
    private Expression $right;

    /**
     * Plus constructor.
     *
     * @param Expression $left  The left-hand of the expression
     * @param Expression $right The right-hand of the expression
     */
    public function __construct(Expression $left, Expression $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf("(%s %s %s)", $this->left->toQuery(), $this->getOperator(), $this->right->toQuery());
    }

    /**
     * Returns the operator. For instance, this function would return "+" for the addition operator.
     *
     * @return string
     */
    abstract protected function getOperator(): string;
}