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
use WikibaseSolutions\CypherDSL\Assignment;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\SetClause
 */
class SetClauseTest extends TestCase
{
	use TestHelper;

	public function testEmptyClause()
	{
		$set = new SetClause();

		$this->assertSame("", $set->toQuery());
	}

	public function testSinglePattern()
	{
		$set = new SetClause();
		$expression = $this->getQueryConvertableMock(Assignment::class, "(a)");

		$set->addAssignment($expression);

		$this->assertSame("SET (a)", $set->toQuery());
	}

	public function testMultiplePattern()
	{
		$set = new SetClause();
		$expressionA = $this->getQueryConvertableMock(Assignment::class, "(a)");
		$expressionB = $this->getQueryConvertableMock(Assignment::class, "(b)");

		$set->addAssignment($expressionA);
		$set->addAssignment($expressionB);

		$this->assertSame("SET (a), (b)", $set->toQuery());
	}

	/**
	 * @doesNotPerformAssertions
	 */
	public function testAcceptsAssignment()
	{
		$set = new SetClause();
		$expression = $this->getQueryConvertableMock(Assignment::class, "(a)");

		$set->addAssignment($expression);

		$set->toQuery();
	}

	public function testDoesNotAcceptAnyType()
	{
		$set = new SetClause();
		$expression = $this->getQueryConvertableMock(AnyType::class, "(a)");

		$this->expectException(TypeError::class);

		$set->addAssignment($expression);

		$set->toQuery();
	}
}