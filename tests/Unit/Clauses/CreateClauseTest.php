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

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\CreateClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\CreateClause
 */
class CreateClauseTest extends TestCase
{
	use TestHelper;

	public function testEmptyClause()
	{
		$createClause = new CreateClause();

		$this->assertSame("", $createClause->toQuery());
	}

	public function testSinglePattern()
	{
		$createClause = new CreateClause();
		$pattern = $this->getQueryConvertableMock(StructuralType::class, "(a)");

		$createClause->addPattern($pattern);

		$this->assertSame("CREATE (a)", $createClause->toQuery());
	}

	public function testMultiplePatterns()
	{
		$createClause = new CreateClause();

		$patternA = $this->getQueryConvertableMock(StructuralType::class, "(a)");
		$patternB = $this->getQueryConvertableMock(StructuralType::class, "(b)");

		$createClause->addPattern($patternA);
		$createClause->addPattern($patternB);

		$this->assertSame("CREATE (a), (b)", $createClause->toQuery());
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testAcceptsNodeType()
	{
		$createClause = new CreateClause();

		$patternA = $this->getQueryConvertableMock(NodeType::class, "(a)");

		$createClause->addPattern($patternA);
		$createClause->toQuery();
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testAcceptsPathType()
	{
		$createClause = new CreateClause();

		$patternA = $this->getQueryConvertableMock(PathType::class, "(a)");

		$createClause->addPattern($patternA);
		$createClause->toQuery();
	}

	public function testDoesNotAcceptAnyType()
	{
		$createClause = new CreateClause();

		$patternA = $this->getQueryConvertableMock(AnyType::class, "(a)");

		$this->expectException(TypeError::class);

		$createClause->addPattern($patternA);
		$createClause->toQuery();
	}
}