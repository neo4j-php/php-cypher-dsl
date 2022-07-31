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
use WikibaseSolutions\CypherDSL\Expressions\PropertyMap;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * Trait used by patterns that can have properties. These are:
 *
 * - node
 * - relationship
 */
trait PatternPropertiesTrait
{
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
        $this->assertClass('properties', [MapType::class, 'array'], $properties);
        $this->properties = is_array($properties) ? Query::map($properties) : $properties;

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
            $this->properties = new PropertyMap();
        } elseif (!$this->properties instanceof PropertyMap) {
            throw new TypeError('$this->properties must be of type PropertyMap to support "addProperty"');
        }

        $this->properties->addProperty($key, $property);

        return $this;
    }

    /**
     * Add the given properties to the object. This is only possible if the properties in this object are a
     * property map. An exception will be thrown if they are anything else (such as a variable). If the object
     * does not yet contain any properties, a new property map will be created.
     *
     * @param PropertyMap|array $properties
     * @return $this
     */
    public function addProperties($properties): self
    {
        self::assertClass('properties', [PropertyMap::class, 'array'], $properties);

        if (!isset($this->properties)) {
            $this->properties = new PropertyMap();
        } elseif (!$this->properties instanceof PropertyMap) {
            throw new TypeError('$this->properties must be of type PropertyMap to support "addProperty"');
        }

        if (!$properties instanceof PropertyMap) {
            $properties = new PropertyMap($properties);
        }

        $this->properties->mergeWith($properties);

        return $this;
    }
}