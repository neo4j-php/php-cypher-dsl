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

use WikibaseSolutions\CypherDSL\Expressions\Expression;

/**
 * This class represents a map of properties. For example, this class can represent the following
 * construct:
 *
 * {name: 'Andy', sport: 'Brazilian Ju-Jitsu'}
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-properties
 * @package WikibaseSolutions\CypherDSL
 */
class PropertyMap implements QueryConvertable
{
	use Escape;

	/**
	 * @var array The map of properties
	 */
	private array $properties;

	/**
	 * Map constructor.
	 *
	 * @param Expression[] $properties The map of properties as a number of key-expression pairs
	 */
	public function __construct(array $properties)
	{
		$this->properties = array_filter($properties);
	}

	/**
	 * Converts the map to a string that may be used directly in a Cypher query.
	 *
	 * @return string
	 */
	public function toQuery(): string
	{
		$pairs = [];

		foreach ($this->properties as $key => $value) {
			$pairs[] = sprintf("%s: %s", $this->escape(strval($key)), $value->toQuery());
		}

		return sprintf("{%s}", implode(", ", $pairs));
	}
}