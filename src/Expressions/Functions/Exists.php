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

namespace WikibaseSolutions\CypherDSL\Expressions\Functions;

use WikibaseSolutions\CypherDSL\Expressions\Types\AnyType;
use WikibaseSolutions\CypherDSL\Expressions\Types\BooleanType;

/**
 * This class represents the "exists()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/predicate/#functions-exists
 */
class Exists extends FunctionCall implements BooleanType
{
    /**
     * @var AnyType A pattern or property
     */
    private AnyType $expression;

    /**
     * Exists constructor. The signature of the "exists()" function is:
     *
     * exists(input :: ANY?) :: (BOOLEAN?)
     *
     * @param AnyType $expression A pattern or property
     */
    public function __construct(AnyType $expression)
    {
        $this->expression = $expression;
    }


    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return "exists(%s)";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return [$this->expression];
    }
}