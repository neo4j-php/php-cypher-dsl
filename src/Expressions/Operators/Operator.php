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

/**
 * This class represents the application of an operator, such as "NOT" or "*".
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 46)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/
 */
abstract class Operator implements QueryConvertible
{
    /**
     * @var bool Whether to insert parentheses around the expression
     */
    private bool $insertParentheses;

    /**
     * UnaryOperator constructor.
     *
     * @param bool $insertParentheses Whether to insert parentheses around the application of the operator
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(bool $insertParentheses = true)
    {
        $this->insertParentheses = $insertParentheses;
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
        $format = $this->insertParentheses ? "(%s)" : "%s";
        $inner = $this->toInner();

        return sprintf($format, $inner);
    }

    /**
     * Returns the inner part of the application of the operator, that is, without any parentheses.
     *
     * @return string
     * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    abstract protected function toInner(): string;
}
