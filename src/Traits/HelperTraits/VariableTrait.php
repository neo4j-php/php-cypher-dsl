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

namespace WikibaseSolutions\CypherDSL\Traits\HelperTraits;

use WikibaseSolutions\CypherDSL\Expressions\Variable;

/**
 * Used for getting and setting variables in patterns (nodes, paths and relationships).
 */
trait VariableTrait
{
    use ErrorTrait;

	/**
	 * @var Variable|null The variable that this object is assigned
	 */
	private ?Variable $variable = null;

	/**
	 * Sets the variable to the one given. If the variable is null, it will be unset.
	 *
	 * @param Variable|string|null $variable
	 *
	 * @return static
	 */
	public function setVariable($variable): self
	{
		self::assertClass('variable', ['string', Variable::class, 'null'], $variable);

		if ($variable === null) {
			unset($this->variable);
		} else {
			$this->variable = is_string($variable) ? new Variable($variable) : $variable;
		}

		return $this;
	}

	/**
	 * Returns the variable of the object. This function generates a variable if none has been set.
	 *
	 * @return Variable|null
	 */
	public function getVariable(): Variable
	{
		if (!isset($this->variable)) {
			$this->variable = new Variable();
		}

		return $this->variable;
	}

	/**
	 * Returns true if and only if this object has a variable explicitly or implicitly set. Note that
	 * "getVariable" will always return a variable, since it generates a variable if none has been set.
	 *
	 * @return bool
	 */
	public function hasVariable(): bool {
		return isset( $this->variable );
	}

	/**
	 * Sets the variable to the one given. If the variable is null, it will be unset.
	 *
	 * @param Variable|string|null $variable
	 *
	 * @return static
	 * @deprecated Use self::setVariable() instead
	 */
	public function named($variable): self
	{
		return $this->setVariable($variable);
	}

}
