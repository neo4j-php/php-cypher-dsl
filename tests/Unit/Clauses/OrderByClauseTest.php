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
use WikibaseSolutions\CypherDSL\Clauses\OrderByClause;
use WikibaseSolutions\CypherDSL\Expressions\Property;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\OrderByClause
 */
class OrderByClauseTest extends TestCase
{
	use ClauseTestHelper;

	public function testEmptyClause()
	{
		$orderBy = new OrderByClause();

		$this->assertSame("", $orderBy->toQuery());
	}

	public function testSingleProperty()
	{
		$orderBy = new OrderByClause();
		$orderBy->addProperty($this->getPropertyMock("a.a", $this));

		$this->assertSame("ORDER BY a.a", $orderBy->toQuery());
	}

	public function testMultipleProperties()
	{
		$orderBy = new OrderByClause();
		$orderBy->addProperty($this->getPropertyMock("a.a", $this));
		$orderBy->addProperty($this->getPropertyMock("a.b", $this));

		$this->assertSame("ORDER BY a.a, a.b", $orderBy->toQuery());
	}

	public function testSinglePropertyDesc()
	{
		$orderBy = new OrderByClause();
		$orderBy->addProperty($this->getPropertyMock("a.a", $this));
		$orderBy->setDescending();

		$this->assertSame("ORDER BY a.a DESCENDING", $orderBy->toQuery());
	}

	public function testMultiplePropertiesDesc()
	{
		$orderBy = new OrderByClause();
		$orderBy->addProperty($this->getPropertyMock("a.a", $this));
		$orderBy->addProperty($this->getPropertyMock("a.b", $this));
		$orderBy->setDescending();

		$this->assertSame("ORDER BY a.a, a.b DESCENDING", $orderBy->toQuery());
	}
}