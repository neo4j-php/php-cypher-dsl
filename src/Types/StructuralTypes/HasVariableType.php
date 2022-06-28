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

namespace WikibaseSolutions\CypherDSL\Types\StructuralTypes;

use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Traits\HasVariableTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * Represents a structural type in Cypher that can have a variable as name.
 *
 * Those are:
 *
 * - node
 * - relationship
 * - path
 *
 * This is a partial type and provides a way to match parameters based on whether they have a variable.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types
 */
interface HasVariableType
{
    /**
     * Names the structural type.
     *
     * @param Variable|string $nameOrVariable The name to give this structural type
     *
     * @return static
     */
    public function named($nameOrVariable): self;

    /**
     * Returns the name of this object, or generates a new name if this object does not yet have a name.
     *
     * @return Variable
     */
    public function getVariable(): Variable;

    /**
     * Returns true if this object has a variable assigned.
     *
     * @return bool
     */
    public function hasVariable(): bool;
}
