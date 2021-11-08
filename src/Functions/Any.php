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

namespace WikibaseSolutions\CypherDSL\Functions;

use WikibaseSolutions\CypherDSL\Traits\BooleanTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\ListType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * Represents the "any()" function.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/functions/predicate/#functions-any
 */
class Any extends FunctionCall implements BooleanType
{
	use BooleanTypeTrait;

	/**
	 * @var Variable A variable that can be used from within the predicate
	 */
	private Variable $variable;

	/**
	 * @var ListType A list
	 */
	private ListType $list;

	/**
	 * @var AnyType A predicate that is tested against all items in the list
	 */
	private AnyType $predicate;

	/**
	 * Any constructor. The signature of the "any()" function is:
	 *
	 * any(variable :: VARIABLE IN list :: LIST OF ANY? WHERE predicate :: ANY?) :: (BOOLEAN?)
	 *
	 * @param Variable $variable A variable that can be used from within the predicate
	 * @param ListType $list A list
	 * @param AnyType $predicate A predicate that is tested against all items in the list
	 */
	public function __construct(Variable $variable, ListType $list, AnyType $predicate)
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
		return "any(%s IN %s WHERE %s)";
	}

	/**
	 * @inheritDoc
	 */
	protected function getParameters(): array
	{
		return [$this->variable, $this->list, $this->predicate];
	}
}