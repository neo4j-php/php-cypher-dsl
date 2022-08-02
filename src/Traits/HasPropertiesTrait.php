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

use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * Trait used by objects that can contain properties (such as a relationship or a node).
 */
trait HasPropertiesTrait
{
    use CastTrait;
    use ErrorTrait;

    /**
     * @var MapType|null The properties of this object
     */
    private ?MapType $properties = null;

    /**
     * Set the properties of this object.
     *
     * @param MapType|array $properties
     * @return $this
     */
    public function withProperties($properties): self
    {
        $this->properties = self::toMapType($properties);

        return $this;
    }

    /**
     * Add a property to the properties in this object. This is only possible if the properties in this object are a
     * property map. An exception will be thrown if they are anything else (such as a variable). If the object
     * does not yet contain any properties, a new property map will be created.
     *
     * @param string $key
     * @param mixed $property
     * @return $this
     */
    public function addProperty(string $key, $property): self
    {
        if (!isset($this->properties)) {
            $this->properties = new Map();
        } elseif (!$this->properties instanceof Map) {
            // Adding to a map is not natively supported by the MapType, but it is supported by Map. Syntactically, it
            // is not possible to add new items to, for instance, a Variable, eventhough it implements MapType. It is
            // however still useful to be able to add items to objects where a Map is used (that is, an object of
            // MapType with the {} syntax).
            throw new TypeError('$this->properties must be of type Map to support "addProperty"');
        }

        $this->properties->addProperty($key, $property);

        return $this;
    }

    /**
     * Add the given properties to the object. This is only possible if the properties in this object are a
     * property map. An exception will be thrown if they are anything else (such as a variable). If the object
     * does not yet contain any properties, a new property map will be created.
     *
     * @param Map|array $properties
     * @return $this
     */
    public function addProperties($properties): self
    {
        self::assertClass('properties', [Map::class, 'array'], $properties);

        if (!isset($this->properties)) {
            $this->properties = new Map();
        } elseif (!$this->properties instanceof Map) {
            throw new TypeError('$this->properties must be of type PropertyMap to support "addProperty"');
        }

        if (is_array($properties)) {
            // Cast the array to a Map
            $properties = new Map($properties);
        }

        $this->properties->mergeWith($properties);

        return $this;
    }

    /**
     * Returns the properties of this object.
     *
     * @return MapType|null
     */
    public function getProperties(): ?MapType
    {
        return $this->properties;
    }
}
