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

use WikibaseSolutions\CypherDSL\Types\AnyType;
use function sprintf;

/**
 * Represents aliasing an expression or variable.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/return/#return-column-alias
 * @see https://neo4j.com/docs/cypher-manual/current/clauses/with/#with-introduce-variables
 */
class Alias implements AnyType
{
    /**
     * @var AnyType The original item to be aliased
     */
    private AnyType $original;

    /**
     * @var Variable The new variable aliasing the original
     */
    private Variable $variable;

    /**
     * Alias constructor.
     *
     * @param AnyType $original The original item to be aliased
     * @param Variable $variable The new variable aliasing the original
     */
    public function __construct(AnyType $original, Variable $variable)
    {
        $this->original = $original;
        $this->variable = $variable;
    }

	/**
	 * Gets the original item of the alias.
	 *
	 * @return AnyType
	 */
	public function getOriginal(): AnyType
	{
		return $this->original;
	}

	/**
	 * Gets the variable from the alias.
	 *
	 * @return Variable
	 */
	public function getVariable(): Variable
	{
		return $this->variable;
	}

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        return sprintf("%s AS %s", $this->original->toQuery(), $this->variable->toQuery());
    }
}
