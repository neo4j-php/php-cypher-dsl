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

use WikibaseSolutions\CypherDSL\Expressions\Operators\In;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\AnyTypeTrait;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * This trait provides a default implementation to satisfy the "PropertyType" interface.
 *
 * This trait should not be used by any class directly. Instead, the following subtraits should be used where
 * appropriate:
 *
 * - BooleanTypeTrait
 * - DateTimeTypeTrait
 * - DateTypeTrait
 * - LocalDateTimeTypeTrait
 * - LocalTimeTypeTrait
 * - NumeralTypeTrait
 * - PointTypeTrait
 * - StringTypeTrait
 * - TimeTypeTrait
 *
 * @implements PropertyType
 */
trait PropertyTypeTrait
{
    use AnyTypeTrait;
    use CastTrait;

    /**
     * @inheritDoc
     */
    public function in($right, bool $insertParentheses = true): In
    {
        return new In($this, self::toListType($right), $insertParentheses);
    }
}
