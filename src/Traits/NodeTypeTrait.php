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

use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;

/**
 * This trait should be used by any expression that returns a node.
 */
trait NodeTypeTrait
{
    use HasPropertiesTrait;
    use HasVariableTrait;

    public function relationship(RelationshipType $relationship, NodeType $node): Path
    {
        return (new Path($this))->relationship($relationship, $node);
    }

    public function relationshipTo(NodeType $node, ?string $type = null, $properties = null, $name = null): Path
    {
        return (new Path($this))->relationshipTo($node, $type, $properties, $name);
    }

    public function relationshipFrom(NodeType $node, ?string $type = null, $properties = null, $name = null): Path
    {
        return (new Path($this))->relationshipFrom($node, $type, $properties, $name);
    }

    public function relationshipUni(NodeType $node, ?string $type = null, $properties = null, $name = null): Path
    {
        return (new Path($this))->relationshipUni($node, $type, $properties, $name);
    }
}
