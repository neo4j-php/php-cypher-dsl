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

use WikibaseSolutions\CypherDSL\Variable;

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
	 * @inheritDoc
	 */
	public function setVariable($variable): self
	{
		self::assertClass('variable', ['string', Variable::class], $variable);

		$this->variable = is_string($variable) ? new Variable($variable) : $variable;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getVariable(): Variable
	{
		if (!isset($this->variable)) {
			$this->variable = new Variable();
		}

		return $this->variable;
	}

	/**
	 * @inheritDoc
	 */
	public function hasVariable(): bool {
		return isset( $this->variable );
	}

	/**
	 * @inheritDoc
	 */
	public function named($nameOrVariable): self
	{
		return $this->setVariable($nameOrVariable);
	}

}
