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

namespace WikibaseSolutions\CypherDSL\Patterns;

use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * This interface represents any pattern that can be related to another pattern using a relationship. These are:
 *
 * - node
 * - path
 */
interface Relatable
{
    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using the given relationship
     * pattern.
     *
     * @param Relationship $relationship The relationship to use
     * @param Relatable $relatable The relatable pattern to attach to this pattern
     *
     * @return Path
     */
    public function relationship(Relationship $relationship, Relatable $relatable): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a right (-->)
     * relationship.
     *
     * @param Relatable $relatable The relatable pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param MapType|array|null $properties The properties to attach to the relationship
     * @param Variable|string|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipTo(Relatable $relatable, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a left (<--)
     * relationship.
     *
     * @param Relatable $relatable The relatable pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param MapType|array|null $properties The properties to attach to the relationship
     * @param Variable|string|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipFrom(Relatable $relatable, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Forms a new path by adding the given relatable pattern to the end of this pattern using a unidirectional
     * (--/<-->) relationship.
     *
     * @param Relatable $relatable The relatable pattern to attach to the end of this pattern
     * @param string|null $type The type of the relationship
     * @param MapType|array|null $properties The properties to attach to the relationship
     * @param Variable|string|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipUni(Relatable $relatable, ?string $type = null, $properties = null, $name = null): Path;
}