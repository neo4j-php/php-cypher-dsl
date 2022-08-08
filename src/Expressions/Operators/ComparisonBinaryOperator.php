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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * Represents a comparison binary operator. These are:
 *
 * - equality: "="
 * - inequality: "<>"
 * - less than: "<"
 * - greater than: ">"
 * - less than or equal to: "<="
 * - greater than or equal to: ">="
 *
 * In additional, there are some string-specific comparison operators:
 *
 * - case-sensitive prefix search on strings: "STARTS WITH"
 * - case-sensitive suffix search on strings: "ENDS WITH"
 * - case-sensitive inclusion search in strings: "CONTAINS"
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 48)
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison
 */
abstract class ComparisonBinaryOperator extends BinaryOperator implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * ComparisonBinaryOperator constructor.
     *
     * @param AnyType $left The left-hand of the comparison operator
     * @param AnyType $right The right-hand of the comparison operator
     * @param bool $insertParentheses Whether to insert parentheses around the expression
	 * @internal This function is not covered by the backwards compatibility guarantee of php-cypher-dsl
     */
    public function __construct(AnyType $left, AnyType $right, bool $insertParentheses = true)
    {
        parent::__construct($left, $right, $insertParentheses);
    }
}
