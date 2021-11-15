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
use WikibaseSolutions\CypherDSL\Clauses\CallProcedureClause;
use WikibaseSolutions\CypherDSL\Tests\Unit\TestHelper;
use WikibaseSolutions\CypherDSL\Types\AnyType;
use WikibaseSolutions\CypherDSL\Variable;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\CallProcedureClause
 */
class CallProcedureClauseTest extends TestCase
{
	use TestHelper;

	public function testEmptyClause()
	{
		$callProcedureClause = new CallProcedureClause();

		$this->assertSame("", $callProcedureClause->toQuery());
	}

	public function testZeroArguments()
	{
		$callProcedureClause = new CallProcedureClause();
		$callProcedureClause->setProcedure("apoc.json");

		$this->assertSame("CALL apoc.json()", $callProcedureClause->toQuery());
	}

	public function testOneArgument()
	{
		$callProcedureClause = new CallProcedureClause();
		$callProcedureClause->setProcedure("apoc.json");

		$callProcedureClause->addArgument($this->getQueryConvertableMock(AnyType::class, "'text'"));

		$this->assertSame("CALL apoc.json('text')", $callProcedureClause->toQuery());
	}

	public function testMultipleArgument()
	{
		$callProcedureClause = new CallProcedureClause();
		$callProcedureClause->setProcedure("apoc.json");

		$expression = $this->getQueryConvertableMock(AnyType::class, "'text'");

		$callProcedureClause->addArgument($expression);
		$callProcedureClause->addArgument($expression);
		$callProcedureClause->addArgument($expression);

		$this->assertSame("CALL apoc.json('text', 'text', 'text')", $callProcedureClause->toQuery());
	}

	public function testWithArguments()
	{
		$callProcedureClause = new CallProcedureClause();
		$callProcedureClause->setProcedure("apoc.json");

		$expression = $this->getQueryConvertableMock(AnyType::class, "'text'");

		$callProcedureClause->addArgument($expression);
		$callProcedureClause->addArgument($expression);
		$callProcedureClause->addArgument($expression);

		// This should overwrite the previous calls to addArgument
		$callProcedureClause->withArguments([$expression]);

		$this->assertSame("CALL apoc.json('text')", $callProcedureClause->toQuery());
	}

	public function testWithYield()
	{
		$callProcedureClause = new CallProcedureClause();
		$callProcedureClause->setProcedure("apoc.json");

		$a = $this->getQueryConvertableMock(Variable::class, "a");
		$b = $this->getQueryConvertableMock(Variable::class, "b");
		$c = $this->getQueryConvertableMock(Variable::class, "c");

		// This should overwrite the previous calls to addArgument
		$callProcedureClause->yields([$a, $b, $c]);

		$this->assertSame("CALL apoc.json() YIELD a, b, c", $callProcedureClause->toQuery());
	}
}