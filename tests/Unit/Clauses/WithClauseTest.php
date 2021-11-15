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
use WikibaseSolutions\CypherDSL\Clauses\WithClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\WithClause
 */
class WithClauseTest extends TestCase
{
	use TestHelper;

	public function testEmptyClause()
	{
		$return = new WithClause();

		$this->assertSame("", $return->toQuery());
	}

	public function testSingleEntry()
	{
		$return = new WithClause();
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "a"));

		$this->assertSame("WITH a", $return->toQuery());
	}

	public function testMultipleEntries()
	{
		$return = new WithClause();
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "a"));
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "b"));
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "c"));

		$this->assertSame("WITH a, b, c", $return->toQuery());
	}

	public function testSingleAlias()
	{
		$return = new WithClause();
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "a"), "b");

		$this->assertSame("WITH a AS b", $return->toQuery());
	}

	public function testMultipleAliases()
	{
		$return = new WithClause();
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "a"), "b");
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "b"), "c");

		$this->assertSame("WITH a AS b, b AS c", $return->toQuery());
	}

	public function testMixedAliases()
	{
		$return = new WithClause();
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "a"), "b");
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "c"));
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "b"), "c");

		$this->assertSame("WITH a AS b, c, b AS c", $return->toQuery());
	}

	public function testAliasIsEscaped()
	{
		$return = new WithClause();
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "a"), ":");

		$this->assertSame("WITH a AS `:`", $return->toQuery());
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testAcceptsAnyType()
	{
		$return = new WithClause();
		$return->addEntry($this->getQueryConvertableMock(AnyType::class, "a"), ":");

		$return->toQuery();
	}
}