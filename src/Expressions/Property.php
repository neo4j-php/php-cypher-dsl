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

use WikibaseSolutions\CypherDSL\Escape;

/**
 * Represents a property. A property in Cypher would be something like "n.prop".
 *
 * @package WikibaseSolutions\CypherDSL
 */
class Property implements Expression
{
	use Escape;

	/**
	 * @var Variable The variable to which this property belongs
	 */
	private Variable $variable;

	/**
	 * @var string The name of the variable
	 */
	private string $property;

	/**
	 * Property constructor.
	 *
	 * @param Variable $variable
	 * @param string $property
	 */
	public function __construct(Variable $variable, string $property)
	{
		$this->variable = $variable;
		$this->property = $property;
	}

	/**
	 * @inheritDoc
	 */
	public function toQuery(): string
	{
		return sprintf("%s.%s", $this->variable->toQuery(), $this->escape($this->property));
	}
}