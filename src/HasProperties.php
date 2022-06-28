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

use WikibaseSolutions\CypherDSL\Types\PropertyTypes\PropertyType;

/**
 * Represents an object that can have properties.
 */
interface HasProperties
{
	/**
	 * Add the given property to the properties of this object.
	 *
	 * @param string $key The name of the property
	 * @param PropertyType|string|bool|float|int $value The value of the property
	 *
	 * @return static
	 */
	public function withProperty(string $key, $value): self;

	/**
	 * Add the given properties to the properties of this object.
	 *
	 * @param PropertyMap|(PropertyType|string|bool|float|int)[] $properties
	 *
	 * @return static
	 */
	public function withProperties($properties): self;

	/**
	 * Returns the properties of this object, or NULL if it has no properties.
	 *
	 * @return PropertyMap|null
	 */
	public function getProperties(): ?PropertyMap;
}