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

use WikibaseSolutions\CypherDSL\Expressions\Operators\Conjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Disjunction;
use WikibaseSolutions\CypherDSL\Expressions\Operators\ExclusiveOr;
use WikibaseSolutions\CypherDSL\Expressions\Operators\Negation;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

/**
 * This trait provides a default implementation to satisfy the "BooleanType" interface.
 *
 * @implements BooleanType
 */
trait BooleanTypeTrait
{
    use CastTrait;
    use PropertyTypeTrait;

    /**
     * @inheritDoc
     */
    public function and($right, bool $insertParentheses = true): Conjunction
    {
        return new Conjunction($this, self::toBooleanType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function or($right, bool $insertParentheses = true): Disjunction
    {
        return new Disjunction($this, self::toBooleanType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function xor($right, bool $insertParentheses = true): ExclusiveOr
    {
        return new ExclusiveOr($this, self::toBooleanType($right), $insertParentheses);
    }

    /**
     * @inheritDoc
     */
    public function not(): Negation
    {
        return new Negation($this);
    }
}
