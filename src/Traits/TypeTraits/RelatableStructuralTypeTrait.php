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

namespace WikibaseSolutions\CypherDSL\Traits\TypeTraits;

use WikibaseSolutions\CypherDSL\Expressions\Patterns\Path;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelatableStructuralType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\RelationshipType;

/**
 * This trait provides a default implementation to satisfy the "RelatableStructuralType" interface.
 *
 * @see RelatableStructuralType
 */
trait RelatableStructuralTypeTrait
{
	use StructuralTypeTrait;

	/**
	 * @inheritDoc
	 */
	public function relationship(RelationshipType $relationship, RelatableStructuralType $relatable): Path
	{
		return (new Path($this))->relationship($relationship, $relatable);
	}

	/**
	 * @inheritDoc
	 */
	public function relationshipTo(RelatableStructuralType $relatable, ?string $type = null, $properties = null, $name = null): Path
	{
		return (new Path($this))->relationshipTo($relatable, $type, $properties, $name);
	}

	/**
	 * @inheritDoc
	 */
	public function relationshipFrom(RelatableStructuralType $relatable, ?string $type = null, $properties = null, $name = null): Path
	{
		return (new Path($this))->relationshipFrom($relatable, $type, $properties, $name);
	}

	/**
	 * @inheritDoc
	 */
	public function relationshipUni(RelatableStructuralType $relatable, ?string $type = null, $properties = null, $name = null): Path
	{
		return (new Path($this))->relationshipUni($relatable, $type, $properties, $name);
	}
}
