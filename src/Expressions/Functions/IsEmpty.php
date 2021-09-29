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

use WikibaseSolutions\CypherDSL\Expressions\Expression;

/**
 * This class represents the "isEmpty()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/predicate/#functions-isempty
 */
class IsEmpty extends FunctionCall
{
    /**
     * @var Expression An expression that returns a list
     */
    private Expression $list;

    /**
     * IsEmpty constructor. The signature of the "isEmpty()" function is:
     *
     * isEmpty(input :: LIST? OF ANY?) :: (BOOLEAN?) - to check whether a list is empty
     * isEmpty(input :: MAP?) :: (BOOLEAN?) - to check whether a map is empty
     * isEmpty(input :: STRING?) :: (BOOLEAN?) - to check whether a string is empty
     *
     * @param Expression $list An expression that returns a list
     */
    public function __construct(Expression $list)
    {
        $this->list = $list;
    }

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return "isEmpty(%s)";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return [$this->list];
    }
}