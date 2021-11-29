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
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * This trait should be used by any expression that returns a structural type.
 *
 * @note This trait should not be used by any class directly.
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/values/#structural-types
 */
trait StructuralTypeTrait
{
    /**
     * @var MapType|null
     */
    private ?MapType $properties;

    /**
     * Add the given property to the properties of this node.
     *
     * @param string $key The name of the property
     * @param AnyType $value The value of the property
     * @return Node
     */
    public function withProperty(string $key, AnyType $value): self
    {
        if (!isset($this->properties)) {
            $this->properties = new PropertyMap();
        }

        $this->properties->addProperty($key, $value);

        return $this;
    }

    /**
     * Add the given properties to the properties of this node.
     *
     * @param PropertyMap|array $properties
     * @return Node
     */
    public function withProperties($properties): self
    {
        if (!isset($this->properties)) {
            $this->properties = new PropertyMap();
        }

        if (is_array($properties)) {
            $properties = new PropertyMap($properties);
        } elseif (!($properties instanceof PropertyMap)) {
            throw new InvalidArgumentException("\$properties must either be an array or a PropertyMap object");
        }

        $this->properties = $this->properties->mergeWith($properties);

        return $this;
    }

    /**
     * Creates a new relationship from this node to the given pattern.
     *
     * @param StructuralType $pattern
     * @return Path
     */
    public function relationshipTo(StructuralType $pattern): Path
    {
        return new Path($this, $pattern, Path::DIR_RIGHT);
    }

    /**
     * Creates a new relationship from the given pattern to this node.
     *
     * @param StructuralType $pattern
     * @return Path
     */
    public function relationshipFrom(StructuralType $pattern): Path
    {
        return new Path($this, $pattern, Path::DIR_LEFT);
    }

    /**
     * Creates a new unidirectional relationship between this node and the given pattern.
     *
     * @param StructuralType $pattern
     * @return Path
     */
    public function relationshipUni(StructuralType $pattern): Path
    {
        return new Path($this, $pattern, Path::DIR_UNI);
    }
}
