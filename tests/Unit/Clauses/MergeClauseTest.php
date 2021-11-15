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
use WikibaseSolutions\CypherDSL\Clauses\Clause;
use WikibaseSolutions\CypherDSL\Clauses\MergeClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\NodeType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\PathType;
use WikibaseSolutions\CypherDSL\Types\StructuralTypes\StructuralType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\MergeClause
 */
class MergeClauseTest extends TestCase
{
	use TestHelper;

	public function testEmptyClause()
	{
		$merge = new MergeClause();

		$this->assertSame("", $merge->toQuery());
	}

	public function testPattern()
	{
		$merge = new MergeClause();
		$pattern = $this->getQueryConvertableMock(StructuralType::class, "(a)");

		$merge->setPattern($pattern);

		$this->assertSame("MERGE (a)", $merge->toQuery());
	}

	public function testSetOnCreate()
	{
		$merge = new MergeClause();

		$pattern = $this->getQueryConvertableMock(StructuralType::class, "(a)");
		$clause = $this->getQueryConvertableMock(Clause::class, "SET a = 10");

		$merge->setPattern($pattern);
		$merge->setOnCreate($clause);

		$this->assertSame("MERGE (a) ON CREATE SET a = 10", $merge->toQuery());
	}

	public function testSetOnMatch()
	{
		$merge = new MergeClause();

		$pattern = $this->getQueryConvertableMock(StructuralType::class, "(a)");
		$clause = $this->getQueryConvertableMock(Clause::class, "SET a = 10");

		$merge->setPattern($pattern);
		$merge->setOnMatch($clause);

		$this->assertSame("MERGE (a) ON MATCH SET a = 10", $merge->toQuery());
	}

	public function testSetOnBoth()
	{
		$merge = new MergeClause();

		$pattern = $this->getQueryConvertableMock(StructuralType::class, "(a)");
		$clause = $this->getQueryConvertableMock(Clause::class, "SET a = 10");

		$merge->setPattern($pattern);
		$merge->setOnCreate($clause);
		$merge->setOnMatch($clause);

		$this->assertSame("MERGE (a) ON CREATE SET a = 10 ON MATCH SET a = 10", $merge->toQuery());
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testAcceptsNodeType()
	{
		$merge = new MergeClause();
		$pattern = $this->getQueryConvertableMock(NodeType::class, "(a)");

		$merge->setPattern($pattern);

		$merge->toQuery();
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testAcceptsPathType()
	{
		$merge = new MergeClause();
		$pattern = $this->getQueryConvertableMock(PathType::class, "(a)");

		$merge->setPattern($pattern);

		$merge->toQuery();
	}

	public function testDoesNotAcceptAnyType()
	{
		$merge = new MergeClause();
		$pattern = $this->getQueryConvertableMock(AnyType::class, "(a)");

		$this->expectException(TypeError::class);

		$merge->setPattern($pattern);

		$merge->toQuery();
	}
}