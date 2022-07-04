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
use WikibaseSolutions\CypherDSL\Clauses\CallProcedureClause;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\CallProcedureClause
 */
class CallProcedureClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $callProcedureClause = new CallProcedureClause();

        $this->assertSame("", $callProcedureClause->toQuery());
        $this->assertEquals([], $callProcedureClause->getArguments());
        $this->assertNull($callProcedureClause->getProcedure());
        $this->assertEquals([], $callProcedureClause->getYields());
    }

    public function testZeroArguments(): void
    {
        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure("apoc.json");

        $this->assertSame("CALL apoc.json()", $callProcedureClause->toQuery());
        $this->assertEquals([], $callProcedureClause->getArguments());
        $this->assertEquals('apoc.json', $callProcedureClause->getProcedure());
        $this->assertEquals([], $callProcedureClause->getYields());
    }

    public function testOneArgument(): void
    {
        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure("apoc.json");

        $param = Query::literal('text');
        $callProcedureClause->addArgument($param);

        $this->assertSame("CALL apoc.json('text')", $callProcedureClause->toQuery());
        $this->assertEquals('apoc.json', $callProcedureClause->getProcedure());
        $this->assertEquals([$param], $callProcedureClause->getArguments());
        $this->assertEquals([], $callProcedureClause->getYields());
    }

    public function testMultipleArgument(): void
    {
        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure("apoc.json");

        $expression = Query::literal('text');

        $callProcedureClause->addArgument($expression);
        $callProcedureClause->addArgument($expression);
        $callProcedureClause->addArgument($expression);

        $this->assertSame("CALL apoc.json('text', 'text', 'text')", $callProcedureClause->toQuery());
        $this->assertEquals([$expression, $expression, $expression], $callProcedureClause->getArguments());
        $this->assertEquals('apoc.json', $callProcedureClause->getProcedure());
        $this->assertEquals([], $callProcedureClause->getYields());
    }

    public function testWithArguments(): void
    {
        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure("apoc.json");

        $expression = Query::literal('text');

        $callProcedureClause->addArgument($expression);
        $callProcedureClause->addArgument($expression);
        $callProcedureClause->addArgument($expression);

        // This should overwrite the previous calls to addArgument
        $callProcedureClause->setArguments([$expression]);

        $this->assertSame("CALL apoc.json('text')", $callProcedureClause->toQuery());
        $this->assertEquals([$expression], $callProcedureClause->getArguments());
        $this->assertEquals('apoc.json', $callProcedureClause->getProcedure());
        $this->assertEquals([], $callProcedureClause->getYields());
    }

    public function testWithYield(): void
    {
        $callProcedureClause = new CallProcedureClause();

        $callProcedureClause->setProcedure("apoc.json");

        $a = Query::variable('a');
        $b = Query::variable('b');
        $c = Query::variable('c');

        // This should overwrite the previous calls to addArgument
        $callProcedureClause->setYields([$a, $b, $c]);

        $this->assertSame("CALL apoc.json() YIELD a, b, c", $callProcedureClause->toQuery());
        $this->assertEquals([], $callProcedureClause->getArguments());
        $this->assertEquals('apoc.json', $callProcedureClause->getProcedure());
        $this->assertEquals([$a, $b, $c], $callProcedureClause->getYields());
    }

    public function testYieldDoesNotAcceptAnyType(): void
    {
        $callProcedureClause = new CallProcedureClause();

        $a = Query::literal('a');

        $this->expectException(TypeError::class);

        $callProcedureClause->setYields([$a]);
    }

    public function testArgumentsOnlyAcceptsAnyType(): void
    {
        $callProcedureClause = new CallProcedureClause();

        $a = new class () {};

        $this->expectException(TypeError::class);

        $callProcedureClause->setArguments([$a]);
    }

    public function testProcedureNameIsEscaped(): void
    {
        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure('apoc\\json');

        $this->assertSame('CALL `apoc\\json`()', $callProcedureClause->toQuery());

        $callProcedureClause->setProcedure('apoc\\json.more');

        $this->assertSame('CALL `apoc\\json`.more()', $callProcedureClause->toQuery());

        $callProcedureClause->setProcedure('apoc\\json.more.even\\more');

        $this->assertSame('CALL `apoc\\json`.more.`even\\more`()', $callProcedureClause->toQuery());
    }

	public function testAlias(): void
	{
		$callProcedureClause = new CallProcedureClause();
		$callProcedureClause->setProcedure('db.labels');
		$callProcedureClause->setYields([
			'alias' => new Variable('a')
		]);

		$this->assertSame('CALL db.labels() YIELD a AS alias', $callProcedureClause->toQuery());

		$callProcedureClause->addYield(new Variable('b'), 'alias2');

		$this->assertSame('CALL db.labels() YIELD a AS alias, b AS alias2', $callProcedureClause->toQuery());

		$callProcedureClause->addYield(new Variable('c'));

		$this->assertSame('CALL db.labels() YIELD a AS alias, b AS alias2, c', $callProcedureClause->toQuery());
	}
}
