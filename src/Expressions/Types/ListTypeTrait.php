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

namespace WikibaseSolutions\CypherDSL\Expressions\Types;

use WikibaseSolutions\CypherDSL\Expressions\Equality;
use WikibaseSolutions\CypherDSL\Expressions\Inequality;

/**
 * This trait should be used by any function that returns a list.
 */
trait ListTypeTrait
{
    /**
     * Perform an equality check or an assignment with the given expression.
     *
     * @param  AnyType $right
     * @return Equality
     */
    public function equals(AnyType $right): Equality
    {
        return new Equality($this, $right);
    }

    /**
     * Perform an inequality comparison against the given expression.
     *
     * @param  AnyType $right
     * @return Inequality
     */
    public function notEquals(AnyType $right): Inequality
    {
        return new Inequality($this, $right);
    }
}