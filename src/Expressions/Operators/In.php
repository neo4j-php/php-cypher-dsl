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

use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * Represents the application of the "IN" operator.
 *
 * @see https://s3.amazonaws.com/artifacts.opencypher.org/openCypher9.pdf (page 51)
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/where/#where-in-operator
 */
final class In extends BinaryOperator implements BooleanType
{
    use BooleanTypeTrait;

    /**
     * In constructor.
     *
     * @param PropertyType $left The left-hand of the expression
     * @param ListType $right The right-hand of the expression
     * @param bool $insertParentheses Whether to insert parentheses around the expression
     */
    public function __construct(PropertyType $left, ListType $right, bool $insertParentheses = true)
    {
        parent::__construct($left, $right, $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    protected function getOperator(): string
    {
        return "IN";
    }
}
