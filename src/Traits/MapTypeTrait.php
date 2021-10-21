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

use WikibaseSolutions\CypherDSL\Property;

/**
 * This trait should be used by any expression that returns a map.
 */
trait MapTypeTrait
{
    /**
     * Returns the property of the given name for this expression. For instance, if this expression is the
     * variable "foo", a function call like $expression->property("bar") would yield "foo.bar".
     *
     * @param  string $property
     * @return Property
     */
    public function property(string $property): Property
    {
        return new Property($this, $property);
    }
}