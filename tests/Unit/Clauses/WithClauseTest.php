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

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\WithClause
 */
class WithClauseTest extends TestCase
{
	use ClauseTestHelper;

	public function testEmptyClause()
	{
		$return = new WithClause();

		$this->assertSame("", $return->toQuery());
	}

	public function testSingleEntry()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a", $this));

		$this->assertSame("WITH a", $return->toQuery());
	}

	public function testMultipleEntries()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a", $this));
		$return->addEntry($this->getExpressionMock("b", $this));
		$return->addEntry($this->getExpressionMock("c", $this));

		$this->assertSame("WITH a, b, c", $return->toQuery());
	}

	public function testSingleAlias()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a", $this), "b");

		$this->assertSame("WITH a AS b", $return->toQuery());
	}

	public function testMultipleAliases()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a", $this), "b");
		$return->addEntry($this->getExpressionMock("b", $this), "c");

		$this->assertSame("WITH a AS b, b AS c", $return->toQuery());
	}

	public function testMixedAliases()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a", $this), "b");
		$return->addEntry($this->getExpressionMock("c", $this));
		$return->addEntry($this->getExpressionMock("b", $this), "c");

		$this->assertSame("WITH a AS b, c, b AS c", $return->toQuery());
	}

	public function testAliasIsEscaped()
	{
		$return = new WithClause();
		$return->addEntry($this->getExpressionMock("a", $this), ":");

		$this->assertSame("WITH a AS `:`", $return->toQuery());
	}
}