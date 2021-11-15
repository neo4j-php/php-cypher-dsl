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

namespace WikibaseSolutions\CypherDSL\Traits;

use WikibaseSolutions\CypherDSL\Contains;
use WikibaseSolutions\CypherDSL\EndsWith;
use WikibaseSolutions\CypherDSL\StartsWith;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * This trait should be used by any expression that returns a string.
 */
trait StringTypeTrait
{
	use PropertyTypeTrait;

	/**
	 * Check whether this expression the given expression.
	 *
	 * @param StringType $right
	 * @return Contains
	 */
	public function contains(StringType $right): Contains
	{
		return new Contains($this, $right);
	}

	/**
	 * Perform a suffix string search with the given expression.
	 *
	 * @param StringType $right
	 * @return EndsWith
	 */
	public function endsWith(StringType $right): EndsWith
	{
		return new EndsWith($this, $right);
	}

	/**
	 * Perform a prefix string search with the given expression.
	 *
	 * @param StringType $right
	 * @return StartsWith
	 */
	public function startsWith(StringType $right): StartsWith
	{
		return new StartsWith($this, $right);
	}
}