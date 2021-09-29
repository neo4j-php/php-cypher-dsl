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
 * This class represents the "none()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/predicate/#functions-none
 */
class None extends FunctionCall
{
	/**
	 * @var Expression A variable that can be used from within the predicate
	 */
    private Expression $variable;

	/**
	 * @var Expression An expression that returns a list
	 */
    private Expression $list;

	/**
	 * @var Expression A predicate that is tested against all items in the list
	 */
    private Expression $predicate;

	/**
	 * None constructor. The signature of the "none()" function is:
	 *
	 * none(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
	 *
	 * @param Expression $variable  A variable that can be used from within the predicate
	 * @param Expression $list      An expression that returns a list
	 * @param Expression $predicate A predicate that is tested against all items in the list
	 */
    public function __construct(Expression $variable, Expression $list, Expression $predicate)
    {
        $this->variable = $variable;
        $this->list = $list;
        $this->predicate = $predicate;
    }

    /**
     * @inheritDoc
     */
    protected function getSignature(): string
    {
        return "none(%s IN %s WHERE %s)";
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(): array
    {
        return [$this->variable, $this->list, $this->predicate];
    }
}