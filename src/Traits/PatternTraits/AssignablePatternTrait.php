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

namespace WikibaseSolutions\CypherDSL\Traits\PatternTraits;

use TypeError;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Map;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Traits\CastTrait;
use WikibaseSolutions\CypherDSL\Traits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;

/**
 * This trait provides a default implementation to satisfy the "AssignablePattern" interface.
 *
 * @implements AssignablePatternTrait
 */
trait AssignablePatternTrait
{
    use CastTrait;
    use ErrorTrait;
    use PatternTrait;

    /**
     * @var MapType|null The properties of this object
     */
    private ?MapType $properties = null;

    /**
     * @inheritDoc
     */
    public function property(string $property): Property
    {
        return new Property($this->getVariable(), $property);
    }

    /**
     * @inheritDoc
     */
    public function withProperties($properties): self
    {
        $this->properties = self::toMapType($properties);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addProperty(string $key, $property): self
    {
        $this->makeProperties();

        $this->properties->add($key, $property);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addProperties($properties): self
    {
        self::assertClass('properties', [Map::class, 'array'], $properties);

        $this->makeProperties();

        if (is_array($properties)) {
            // Cast the array to a Map
            $properties = new Map($properties);
        }

        $this->properties->mergeWith($properties);

        return $this;
    }

    private function makeProperties(): void
    {
        if (!isset($this->properties)) {
            $this->properties = new Map();
        } elseif (!($this->properties instanceof Map)) {
            // Adding to a map is not natively supported by the MapType, but it is supported by Map. Syntactically, it
            // is not possible to add new items to, for instance, a Variable, even though it implements MapType. It is
            // however still useful to be able to add items to objects where a Map is used (that is, an object of
            // MapType with the {} syntax).
            throw new TypeError('$this->properties must be of type Map to support "addProperty"');
        }
    }

    /**
     * @inheritDoc
     */
    public function getProperties(): ?MapType
    {
        return $this->properties;
    }
}
