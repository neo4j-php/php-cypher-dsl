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

use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Patterns\Relationship;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\HasRelationshipsType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;

/**
 * This trait provides a default implementation to satisfy the "PathType" interface.
 *
 * @see PathType
 */
trait PathTypeTrait
{
    use HasVariableTypeTrait;

    /**
     * @inheritDoc
     */
    public function relationship(RelationshipType $relationship, HasRelationshipsType $nodeOrPath): Path
    {
        self::assertClass('nodeOrPath', [__CLASS__, Node::class], $nodeOrPath);

        $this->relationships[] = $relationship;
        if ($nodeOrPath instanceof self) {
            $this->relationships = array_merge($this->relationships, $nodeOrPath->getRelationships());
            $this->nodes = array_merge($this->nodes, $nodeOrPath->getNodes());
        } else {
            $this->nodes []= $nodeOrPath;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function relationshipTo(HasRelationshipsType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_RIGHT, $type, $properties, $name);

        return $this->relationship($relationship, $nodeOrPath);
    }

    /**
     * @inheritDoc
     */
    public function relationshipFrom(HasRelationshipsType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_LEFT, $type, $properties, $name);

        return $this->relationship($relationship, $nodeOrPath);
    }

    /**
     * @inheritDoc
     */
    public function relationshipUni(HasRelationshipsType $nodeOrPath, ?string $type = null, $properties = null, $name = null): Path
    {
        $relationship = $this->buildRelationship(Relationship::DIR_UNI, $type, $properties, $name);

        return $this->relationship($relationship, $nodeOrPath);
    }
}
