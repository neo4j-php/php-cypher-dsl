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

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\ErrorTrait;

/**
 * This class represents a pattern.
 *
 * @note A pattern is not an expression, but rather a syntactic construct used for pattern matching on the graph
 *  database.
 */
abstract class Pattern implements QueryConvertible
{
	use ErrorTrait;

	/**
	 * @var Variable|null The variable that this object is assigned
	 */
	protected ?Variable $variable = null;

	/**
	 * Alias of Pattern::setVariable().
	 *
	 * @param Variable|string $variable
	 * @return $this
	 */
	public function withVariable($variable): self
	{
		$this->assertClass('variable', [Variable::class, 'string'], $variable);
		$this->variable = is_string($variable) ? new Variable($variable) : $variable;

		return $this;
	}

	/**
	 * Returns the variable of the object. This function generates a variable if none has been set.
	 *
	 * @return Variable
	 */
	public function getVariable(): Variable
	{
		if (!isset($this->variable)) {
			$this->variable = new Variable();
		}

		return $this->variable;
	}
}