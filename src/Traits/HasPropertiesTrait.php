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

use function is_array;
use WikibaseSolutions\CypherDSL\PropertyMap;
use WikibaseSolutions\CypherDSL\Types\AnyType;

trait HasPropertiesTrait
{
    use ErrorTrait;

    private ?PropertyMap $properties = null;

    /**
     * Add the given property to the properties of this node.
     *
     * @param string $key The name of the property
     * @param AnyType $value The value of the property
     *
     * @return static
     */
    public function withProperty(string $key, AnyType $value): self
    {
        $this->initialiseProperties();

        $this->properties->addProperty($key, $value);

        return $this;
    }

    /**
     * Add the given properties to the properties of this node.
     *
     * @param PropertyMap|array $properties
     *
     * @return static
     */
    public function withProperties($properties): self
    {
        self::assertClass('properties', [PropertyMap::class, 'array'], $properties);

        $this->initialiseProperties();

        $properties = is_array($properties) ? new PropertyMap($properties) : $properties;

        $this->properties->mergeWith($properties);

        return $this;
    }

    public function getProperties(): ?PropertyMap
    {
        return $this->properties;
    }

    /**
     * @return void
     */
    private function initialiseProperties(): void
    {
        if ($this->properties === null) {
            $this->properties = new PropertyMap();
        }
    }
}
