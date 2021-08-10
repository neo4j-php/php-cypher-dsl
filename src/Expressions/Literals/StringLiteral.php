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

namespace WikibaseSolutions\CypherDSL\Expressions\Literals;

use WikibaseSolutions\CypherDSL\Expressions\Expression;

/**
 * Represents a string literal. The name of this class diverges from the naming scheme, because a class
 * in PHP cannot be named "String".
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/expressions/#cypher-expressions-string-literals
 * @package WikibaseSolutions\CypherDSL\Expressions\Literals
 */
class StringLiteral implements Expression
{
	/**
	 * @var string
	 */
	private string $value;

	/**
	 * @var bool Whether to use double quotes or not.
	 */
	private bool $useDoubleQuotes = false;

	/**
	 * StringLiteral constructor.
	 *
	 * @param string $value
	 */
	public function __construct(string $value)
	{
		$this->value = $value;
	}

	/**
	 * Whether to use double quotes or not.
	 *
	 * @param bool $useDoubleQuotes
	 */
	public function useDoubleQuotes(bool $useDoubleQuotes = true) {
		$this->useDoubleQuotes = $useDoubleQuotes;
	}

	/**
	 * @inheritDoc
	 */
	public function toQuery(): string
	{
		// Encode backslashes
		$value = str_replace("\\", "\\\\", $this->value);

		// Encode tabs, newlines, carriage returns and form feeds
		$value = str_replace(["\t", "\n", "\r", "\f"], ["\\t", "\\n", "\\r", "\\f"], $value);

		if ($this->useDoubleQuotes) {
			return sprintf('"%s"', str_replace('"', '\"', $value));
		} else {
			return sprintf("'%s'", str_replace("'", "\'", $value));
		}
	}
}