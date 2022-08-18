<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) 2021  Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\CallProcedureClause;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
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
    }

    public function testSetProcedure(): void
    {
        $procedure = Procedure::localtime();

        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->setProcedure($procedure);

        $this->assertSame("CALL localtime()", $callProcedureClause->toQuery());
    }

    public function testAddSingleYield(): void
    {
        $procedure = Procedure::localtime();

        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->addYield(Query::variable('a'));
        $callProcedureClause->setProcedure($procedure);

        $this->assertSame("CALL localtime() YIELD a", $callProcedureClause->toQuery());
    }

    public function testAddMultipleYields(): void
    {
        $procedure = Procedure::localtime();

        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->addYield(Query::variable('a'));
        $callProcedureClause->addYield(Query::variable('b'));
        $callProcedureClause->setProcedure($procedure);

        $this->assertSame("CALL localtime() YIELD a, b", $callProcedureClause->toQuery());
    }

    public function testAddMultipleYieldsSingleCall(): void
    {
        $procedure = Procedure::localtime();

        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->addYield(Query::variable('a'), Query::variable('b'));
        $callProcedureClause->setProcedure($procedure);

        $this->assertSame("CALL localtime() YIELD a, b", $callProcedureClause->toQuery());
    }

    public function testAddYieldString(): void
    {
        $procedure = Procedure::localtime();

        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->addYield('a');
        $callProcedureClause->setProcedure($procedure);

        $this->assertSame("CALL localtime() YIELD a", $callProcedureClause->toQuery());
    }

    public function testAddYieldWithoutProcedure(): void
    {
        $callProcedureClause = new CallProcedureClause();
        $callProcedureClause->addYield('a');

        $this->assertSame("", $callProcedureClause->toQuery());
    }

    public function testSetProcedureReturnsSameInstance(): void
    {
        $expected = new CallProcedureClause();
        $actual = $expected->setProcedure(Procedure::localtime());

        $this->assertSame($expected, $actual);
    }

    public function testAddYieldReturnsSameInstance(): void
    {
        $expected = new CallProcedureClause();
        $actual = $expected->addYield('a');

        $this->assertSame($expected, $actual);

        $actual = $expected->addYield('b', 'c');

        $this->assertSame($expected, $actual);
    }

    public function testGetProcedure(): void
    {
        $procedure = Procedure::localtime();

        $callProcedureClause = new CallProcedureClause();

        $this->assertNull($callProcedureClause->getProcedure());

        $callProcedureClause->setProcedure($procedure);

        $this->assertSame($procedure, $callProcedureClause->getProcedure());
    }

    public function testGetYields(): void
    {
        $callProcedureClause = new CallProcedureClause();

        $this->assertEmpty($callProcedureClause->getYields());

        $a = Query::variable('a');

        $callProcedureClause->addYield($a);

        $this->assertSame([$a], $callProcedureClause->getYields());

        $b = Query::variable('b');
        $c = Query::variable('c');

        $callProcedureClause->addYield($b, $c);

        $this->assertSame([$a, $b, $c], $callProcedureClause->getYields());
    }

    public function testCanBeEmpty(): void
    {
        $clause = new CallProcedureClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
