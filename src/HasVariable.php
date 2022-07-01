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

/**
 * Represents an object that can have a name (variable).
 */
interface HasVariable
{
	/**
	 * Names the structural type.
	 *
	 * @param Variable|string $nameOrVariable The name to give this structural type
	 *
	 * @return $this
	 *
	 * @deprecated Use setName() instead
	 */
	public function named($nameOrVariable);

	/**
	 * Names the structural type.
	 *
	 * @param Variable|string $nameOrVariable The name to give this structural type
	 *
	 * @return $this
	 */
	public function setVariable($nameOrVariable);

	/**
	 * Returns the name of this object, or generates a new name if this object does not yet have a name.
	 *
	 * @return Variable
	 */
	public function getVariable(): Variable;

	/**
	 * Returns true if this object has a variable assigned.
     *
     * @note Calling $this->getVariable() will always return a variable, even if no variable has explicitly been
     *  assigned to this instance. That is because getVariable() automatically generates a variable if it does not
     *  have one explicitly.
	 *
	 * @return bool
	 */
	public function hasVariable(): bool;
}