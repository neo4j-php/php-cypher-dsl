<?php declare(strict_types=1);
/*
 * This file is part of php-cypher-dsl.
 *
 * Copyright (C) Wikibase Solutions
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WikibaseSolutions\CypherDSL\Tests\Unit\Clauses;

use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\CallClause;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\CallClause
 */
final class CallClauseTest extends TestCase
{
    public function testCallClauseWithoutSubqueryIsEmpty(): void
    {
        $clause = new CallClause();

        $this->assertEquals('', $clause->toQuery());
    }

    public function testCallClauseWithEmptySubqueryIsEmpty(): void
    {
        $query = Query::new();

        $clause = new CallClause();
        $clause->withSubQuery($query);

        $this->assertSame('', $clause->toQuery());

        $clause->addWithVariable(Query::variable('x'));

        $this->assertSame('', $clause->toQuery());
    }

    public function testCallClauseWithoutWithDoesNotHaveWithStatement(): void
    {
        $query = Query::new()->match(Query::node('testing'));

        $clause = new CallClause();
        $clause->withSubQuery($query);

        $this->assertSame('CALL { ' . $query->toQuery() . ' }', $clause->toQuery());
    }

    public function testCallClauseFilled(): void
    {
        $query = Query::new()->match(Query::node('X')->withVariable('x'))->returning(Query::rawExpression('*'));

        $clause = new CallClause();
        $clause->withSubQuery($query);

        $this->assertSame('CALL { MATCH (x:X) RETURN * }', $clause->toQuery());
    }

    public function testCallClauseWithVariables(): void
    {
        $query = Query::new()->match(Query::node('X')->withVariable('x'))->returning(Query::rawExpression('*'));

        $clause = new CallClause();

        $clause->withSubQuery($query);
        $clause->addWithVariable(Query::variable('x'));

        $this->assertSame('CALL { WITH x MATCH (x:X) RETURN * }', $clause->toQuery());

        $clause->addWithVariable(Query::variable('y'));

        $this->assertSame('CALL { WITH x, y MATCH (x:X) RETURN * }', $clause->toQuery());
    }

    public function testAddWithVariableSingleCall(): void
    {
        $clause = new CallClause();
        $clause->withSubQuery(Query::new()->match(Query::node('x')));

        $clause->addWithVariable(Query::variable('a'), Query::variable('b'));

        $this->assertSame('CALL { WITH a, b MATCH (:x) }', $clause->toQuery());
    }

    public function testAddWithVariableSting(): void
    {
        $clause = new CallClause();
        $clause->withSubQuery(Query::new()->match(Query::node('x')));

        $clause->addWithVariable('a');

        $this->assertSame('CALL { WITH a MATCH (:x) }', $clause->toQuery());
    }

    public function testGetSubQuery(): void
    {
        $clause = new CallClause();
        $subQuery = Query::new()->match(Query::node('x'));

        $clause->withSubQuery($subQuery);

        $this->assertSame($subQuery, $clause->getSubQuery());
    }

    public function testGetWithVariables(): void
    {
        $clause = new CallClause();

        $a = Query::variable('a');
        $b = Query::variable('b');
        $c = Query::variable('c');

        $clause->addWithVariable($a, $b);
        $clause->addWithVariable($c);

        $this->assertSame([$a, $b, $c], $clause->getWithVariables());
    }

    public function testWithSubQueryReturnsSameInstance(): void
    {
        $expected = new CallClause();
        $actual = $expected->withSubQuery(Query::new());

        $this->assertSame($expected, $actual);
    }

    public function testAddWithVariableReturnsSameInstance(): void
    {
        $expected = new CallClause();
        $actual = $expected->addWithVariable('a');

        $this->assertSame($expected, $actual);

        $actual = $expected->addWithVariable('a', 'b');

        $this->assertSame($expected, $actual);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new CallClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
