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

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits\PropertyTypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Operators\Contains;
use WikibaseSolutions\CypherDSL\Expressions\Operators\EndsWith;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Regex;
use WikibaseSolutions\CypherDSL\Expressions\Operators\StartsWith;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\StringType;

/**
 * This trait provides a default implementation to satisfy the "StringType" interface.
 *
 * @implements StringType
 */
trait StringTypeTrait
{
	use CastTrait;
	use PropertyTypeTrait;

	/**
	 * @inheritDoc
	 */
    public function contains($right, bool $insertParentheses = true): Contains
    {
        return new Contains($this, self::toStringType($right), $insertParentheses);
    }

	/**
	 * @inheritDoc
	 */
    public function endsWith($right, bool $insertParentheses = true): EndsWith
    {
        return new EndsWith($this, self::toStringType($right), $insertParentheses);
    }

	/**
	 * @inheritDoc
	 */
    public function startsWith($right, bool $insertParentheses = true): StartsWith
    {
        return new StartsWith($this, self::toStringType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function regex($right, bool $insertParentheses = true): Regex
    {
        return new Regex($this, self::toStringType($right), $insertParentheses);
    }
}
