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

use InvalidArgumentException;

/**
 * Trait for encoding certain structures that are used in multiple clauses in a
 * Cypher query.
 *
 * @package WikibaseSolutions\CypherDSL
 */
trait Encoder
{
	/**
	 * Encodes the given key-value list of properties into a string that can be used directly in
	 * a Cypher query.
	 *
	 * @param array $properties
	 * @return string
	 */
	private function encodePropertyList(array $properties): string
	{
		$pairs = [];

		foreach ($properties as $key => $value) {
			$pairs[] = sprintf("%s: %s", $this->escapeName(strval($key)), $this->encodeValue($value));
		}

		return sprintf("{%s}", implode(", ", $pairs));
	}

	/**
	 * Escapes the given value and returns the encoded variant (i.e. with quotes inserted) that
	 * can directly be inserted into a Cypher query.
	 *
	 * @param $value
	 * @return string
	 */
	private function encodeValue($value): string
	{
		if (is_string($value)) {
			// Escape any quotes in the given value and then encapsulate the value with quotes
			return sprintf("'%s'", str_replace("'", "\\'", $value));
		}

		if (is_array($value)) {
			// Encode every value in the array, then put comma's in-between the values and place brackets
			// around the result
			return sprintf(
				"[%s]",
				implode(", ", array_map(fn ($value): string => $this->encodeValue($value), $value))
			);
		}

		return strval($value);
	}

	/**
	 * Escapes the given 'name'. A name is an unquoted literal in a Cypher query, such as variables,
	 * types or property names.
	 *
	 * @param string $name
	 * @return string
	 */
	private function escapeName(string $name): string
	{
		if ($name === "") {
			return "";
		}

		if (ctype_alpha($name)) {
			return $name;
		}

		if (strpos($name, '`') !== false) {
			throw new InvalidArgumentException("A name must not contain a backtick (`)");
		}

		return sprintf("`%s`", $name);
	}
}