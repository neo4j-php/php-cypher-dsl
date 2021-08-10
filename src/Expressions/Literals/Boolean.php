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

/**
 * Represents a boolean (true or false) literal.
 *
 * @package WikibaseSolutions\CypherDSL\Expressions\Literals
 */
class Boolean implements Literal
{
	/**
	 * @var bool The value
	 */
	private bool $value;

	/**
	 * Boolean constructor.
	 *
	 * @param bool $value
	 */
	public function __construct(bool $value)
	{
		$this->value = $value;
	}

	/**
	 * @inheritDoc
	 */
	public function toQuery(): string
	{
		return $this->value ? "true" : "false";
	}
}