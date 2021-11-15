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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Traits;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Traits\StructuralTypeTrait
 */
class StructuralTypeTraitTest extends TestCase
{
	use TestHelper;

	/**
	 * @var MockObject|StructuralType
	 */
	private $a;

	/**
	 * @var MockObject|StructuralType
	 */
	private $b;

	public function setUp(): void
	{
		$this->a = $this->getQueryConvertableMock(StructuralType::class, "10");
		$this->b = $this->getQueryConvertableMock(StructuralType::class, "15");
	}

	public function testRelationshipTo()
	{
		$relationshipTo = $this->a->relationshipTo($this->b);

		$this->assertInstanceOf(Path::class, $relationshipTo);
	}

	public function testRelationshipFrom()
	{
		$relationshipTo = $this->a->relationshipFrom($this->b);

		$this->assertInstanceOf(Path::class, $relationshipTo);
	}

	public function testRelationshipUni()
	{
		$relationshipTo = $this->a->relationshipUni($this->b);

		$this->assertInstanceOf(Path::class, $relationshipTo);
	}
}