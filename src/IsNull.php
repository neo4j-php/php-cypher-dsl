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

namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Traits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use function sprintf;

/**
 * Represents the IS NULL comparison operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison
 */
class IsNull implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * @var AnyType The type to test against null
     */
    private AnyType $expression;
    private bool $insertParentheses;

    /**
     * IS NULL constructor.
     *
     * @param AnyType $expression The type to test against null.
     */
    public function __construct(AnyType $expression, bool $insertParentheses = true)
    {
        $this->expression = $expression;
        $this->insertParentheses = $insertParentheses;
    }

    /**
     * Returns the expression to test against null.
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
    public function toQuery(): string
    {
        return sprintf($this->insertParentheses ? "(%s IS NULL)" : "%s IS NULL", $this->expression->toQuery());
    }

    /**
     * Returns whether or not the operator inserts parenthesis.
     *
     * @return bool
     */
    public function insertsParentheses(): bool
    {
        return $this->insertParentheses;
    }
}