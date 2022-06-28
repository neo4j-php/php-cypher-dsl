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

use WikibaseSolutions\CypherDSL\Traits\TypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\HelperTraits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\CompositeType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * Represents the application of the equality (=) operator.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/operators/#query-operators-comparison
 * @see Assignment For a semantically different, but syntactically identical operator
 */
class Equality extends BinaryOperator implements BooleanType
{
    use BooleanTypeTrait;
    use ErrorTrait;

    /**
     * Equality constructor
     *
     * @param PropertyType|CompositeType $left              The left-hand of the expression
     * @param PropertyType|CompositeType $right             The right-hand of the expression
     * @param bool                       $insertParentheses Whether to insert parentheses around the expression
     */
    public function __construct(Anytype $left, AnyType $right, bool $insertParentheses = true)
    {
        self::assertClass('left', [PropertyType::class, CompositeType::class], $left);
        self::assertClass('right', [PropertyType::class, CompositeType::class], $right);

        parent::__construct($left, $right, $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    protected function getOperator(): string
    {
        return "=";
    }
}
