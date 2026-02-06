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
use WikibaseSolutions\CypherDSL\Clauses\ReturnClause;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Patterns\Path;
use WikibaseSolutions\CypherDSL\Syntax\Alias;
use WikibaseSolutions\CypherDSL\Types\AnyType;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\ReturnClause
 */
final class ReturnClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $return = new ReturnClause();

        $this->assertSame("", $return->toQuery());
        $this->assertSame([], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testSingleColumn(): void
    {
        $return = new ReturnClause();
        $column = $this->createMock(AnyType::class);
        $column->method('toQuery')->willReturn('a');
        $return->addColumn($column);

        $this->assertSame("RETURN a", $return->toQuery());
        $this->assertSame([$column], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testMultipleColumns(): void
    {
        $return = new ReturnClause();

        $columnA = new Variable('a');
        $columnB = (new Path)->withVariable('b');
        $columnC = (new Node)->withVariable('c');

        $return->addColumn($columnA);
        $return->addColumn($columnB);
        $return->addColumn($columnC);

        $this->assertSame("RETURN a, b, c", $return->toQuery());
        $this->assertSame([$columnA, $columnB->getVariable(), $columnC->getVariable()], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testSingleAlias(): void
    {
        $return = new ReturnClause();
        $column = new Alias(new Variable('a'), new Variable('b'));
        $return->addColumn($column);

        $this->assertSame("RETURN a AS b", $return->toQuery());
        $this->assertSame([$column], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testMultipleAliases(): void
    {
        $return = new ReturnClause();
        $aliasA = new Alias(new Variable('a'), new Variable('b'));
        $aliasB = new Alias(new Variable('b'), new Variable('c'));
        $return->addColumn($aliasA);
        $return->addColumn($aliasB);

        $this->assertSame("RETURN a AS b, b AS c", $return->toQuery());
        $this->assertSame([$aliasA, $aliasB], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testMixedAliases(): void
    {
        $return = new ReturnClause();
        $columnA = new Alias(new Variable('a'), new Variable('b'));
        $columnB = new Variable('c');
        $columnC = new Alias(new Variable('b'), new Variable('c'));
        $return->addColumn($columnA);
        $return->addColumn($columnB);
        $return->addColumn($columnC);

        $this->assertSame("RETURN a AS b, c, b AS c", $return->toQuery());
        $this->assertEquals([$columnA, $columnB, $columnC], $return->getColumns());
        $this->assertFalse($return->isDistinct());
    }

    public function testReturnDistinct(): void
    {
        $return = new ReturnClause();
        $column = new Variable('a');
        $return->addColumn($column);
        $return->setDistinct();

        $this->assertSame("RETURN DISTINCT a", $return->toQuery());
        $this->assertSame([$column], $return->getColumns());
        $this->assertTrue($return->isDistinct());
    }

    public function testAddColumnLiteral(): void
    {
        $return = new ReturnClause();

        $return->addColumn(100);
        $return->addColumn("hello world");

        $this->assertSame("RETURN 100, 'hello world'", $return->toQuery());
    }

    public function testAddColumnReturnsSameInstance(): void
    {
        $expected = new ReturnClause();
        $actual = $expected->addColumn(100);

        $this->assertSame($expected, $actual);
    }

    public function testSetDistinctReturnsSameInstance(): void
    {
        $expected = new ReturnClause();
        $actual = $expected->setDistinct();

        $this->assertSame($expected, $actual);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new ReturnClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
