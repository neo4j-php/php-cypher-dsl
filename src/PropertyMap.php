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

namespace WikibaseSolutions\CypherDSL;

use WikibaseSolutions\CypherDSL\Literals\Literal;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\ErrorTrait;
use WikibaseSolutions\CypherDSL\Traits\HelperTraits\EscapeTrait;
use WikibaseSolutions\CypherDSL\Traits\TypeTraits\MapTypeTrait;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\CompositeTypes\MapType;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * This class represents a map of properties. For example, this class can represent the following
 * construct:
 *
 * {name: 'Andy', sport: 'Brazilian Ju-Jitsu'}
 *
 * @see https://neo4j.com/docs/cypher-manual/current/syntax/patterns/#cypher-pattern-properties
 */
class PropertyMap implements MapType
{
    use EscapeTrait;
    use ErrorTrait;
    use MapTypeTrait;

    /**
     * @var array The map of properties
     */
    public array $properties = [];

    /**
     * PropertyMap constructor.
     *
     * @param AnyType[] $properties The map of properties as a number of key-expression pairs
     */
    public function __construct(array $properties = [])
    {
		foreach ($properties as $property) {
			$this->assertClass('properties', AnyType::class, $property);
		}

        $this->properties = $properties;
    }

    /**
     * Adds a property for the given name with the given value. Overrides the property if it already exists.
     *
     * @param string $key The name of the property
     * @param PropertyType|string|int|bool|float $value The value of the property
     * @return PropertyMap
     */
    public function addProperty(string $key, $value): self
    {
        if (!$value instanceof PropertyType) {
            $value = Literal::literal($value);
        }

        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * Merges the given PropertyMap with this PropertyMap.
     *
     * @param PropertyMap $propertyMap
     * @return PropertyMap
     */
    public function mergeWith(PropertyMap $propertyMap): self
    {
        $this->properties = array_merge($this->properties, $propertyMap->properties);

        return $this;
    }

    /**
     * Returns the map of properties as a number of key-expression pairs.
     *
     * @return AnyType[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @inheritDoc
     */
    public function toQuery(): string
    {
        $pairs = [];

        foreach ($this->properties as $key => $value) {
            $pairs[] = sprintf("%s: %s", $this->escape(strval($key)), $value->toQuery());
        }

        return sprintf("{%s}", implode(", ", $pairs));
    }
}
