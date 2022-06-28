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

use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * Represents a structural type in Cypher that can have relationships.
 *
 * Those are:
 *
 * - node
 * - path
 *
 * This is a partial type and provides a way to match parameters based on whether they allow for relationships.
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types
 */
interface HasRelationshipsType
{
    /**
     * Adds a new relationship from the end of the structural type to the node pattern.
     *
     * @param RelationshipType $relationship
     * @param Node|Path $nodeOrPath
     *
     * @return Path
     */
    public function relationship(RelationshipType $relationship, HasRelationshipsType $nodeOrPath): Path;

    /**
     * Adds a new relationship to the node pattern at the end of the structural type to form a path.
     *
     * @param NodeType|Path $nodeOrPath The node to attach to the end of the structural type
     * @param string|null $type The type of the relationship
     * @param array|PropertyMap|null $properties The properties to attach to the relationship
     * @param string|Variable|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipTo(HasRelationshipsType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Adds a new relationship from the node pattern at the end of the structural type to form a path.
     *
     * @param NodeType|Path $nodeOrPath The node to attach to the end of the structural type.
     * @param string|null $type The type of the relationship
     * @param array|PropertyMap|null $properties The properties to attach to the relationship
     * @param string|Variable|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipFrom(HasRelationshipsType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path;

    /**
     * Adds a new unidirectional relationship to the node pattern at the end of the structural type to form a path.
     *
     * @param NodeType|Path $nodeOrPath The node to attach to the end of the structural type.
     * @param string|null $type The type of the relationship
     * @param array|PropertyMap|null $properties The properties to attach to the relationship
     * @param string|Variable|null $name The name fo the relationship
     *
     * @return Path
     */
    public function relationshipUni(HasRelationshipsType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path;
}
