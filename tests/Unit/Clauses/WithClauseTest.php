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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\WithClause;
use WikibaseSolutions\CypherDSL\Expressions\Expression;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\WithClause
 */
class WithClauseTest extends TestCase
{
	public function testEmptyClause()
	{
		$return = new WithClause();

		$this->assertSame("", $return->toQuery());
	}

	public function testSingleEntry()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a"));

		$this->assertSame("WITH a", $return->toQuery());
	}

	public function testMultipleEntries()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a"));
		$return->addEntry($this->getExpressionMock("b"));
		$return->addEntry($this->getExpressionMock("c"));

		$this->assertSame("WITH a, b, c", $return->toQuery());
	}

	public function testSingleAlias()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a"), "b");

		$this->assertSame("WITH a AS b", $return->toQuery());
	}

	public function testMultipleAliases()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a"), "b");
		$return->addEntry($this->getExpressionMock("b"), "c");

		$this->assertSame("WITH a AS b, b AS c", $return->toQuery());
	}

	public function testMixedAliases()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a"), "b");
		$return->addEntry($this->getExpressionMock("c"));
		$return->addEntry($this->getExpressionMock("b"), "c");

		$this->assertSame("WITH a AS b, c, b AS c", $return->toQuery());
	}

	public function testAliasIsEscaped()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a"), ":");

		$this->assertSame("WITH a AS `:`", $return->toQuery());
	}

	/**
	 * Returns a mock of the Expression class that returns the given string when toQuery() is called.
	 *
	 * @param string $variable
	 * @return Expression|MockObject
	 */
	private function getExpressionMock(string $variable): Expression
	{
		$mock = $this->getMockBuilder(Expression::class)->getMock();
		$mock->method('toQuery')->willReturn($variable);

		return $mock;
	}
}