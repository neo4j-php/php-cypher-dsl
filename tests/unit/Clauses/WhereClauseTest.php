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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Expressions\Literals\Literal;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\WhereClause
 */
final class WhereClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $where = new WhereClause();

        $this->assertSame("", $where->toQuery());
        $this->assertNull($where->getExpression());
    }

    public function testAddBooleanType(): void
    {
        $where = new WhereClause();

        $where->addExpression(Literal::boolean(true));

        $this->assertSame("WHERE true", $where->toQuery());
    }

    public function testAddBooleanLiteral(): void
    {
        $where = new WhereClause();

        $where->addExpression(true);

        $this->assertSame("WHERE true", $where->toQuery());
    }

    public function testExpressionChainingDefaultAnd(): void
    {
        $where = new WhereClause();

        $where->addExpression(true);
        $where->addExpression(true);
        $where->addExpression(false);

        $this->assertSame("WHERE ((true AND true) AND false)", $where->toQuery());
    }

    public function testExpressionChaining(): void
    {
        $where = new WhereClause();

        $where->addExpression(true);
        $where->addExpression(true, WhereClause::OR);
        $where->addExpression(true, WhereClause::AND);
        $where->addExpression(true, WhereClause::XOR);

        $this->assertSame('WHERE (((true OR true) AND true) XOR true)', $where->toQuery());
    }

    public function testExpressionChainingFirstArgumentDoesNothing(): void
    {
        $where = new WhereClause();

        $where->addExpression(true, WhereClause::OR);

        $this->assertSame('WHERE true', $where->toQuery());
    }

    public function testExpressionChainingInvalidOperator1(): void
    {
        $where = new WhereClause();

        $this->expectException(InvalidArgumentException::class);

        $where->addExpression(true, 'bad');
    }

    public function testExpressionChainingInvalidOperator2(): void
    {
        $where = new WhereClause();

        $where->addExpression(true);

        $this->expectException(InvalidArgumentException::class);

        $where->addExpression(false, 'bad');
    }

    public function testGetExpression(): void
    {
        $expression = Literal::boolean(true);

        $where = new WhereClause();

        $where->addExpression($expression);

        $this->assertSame($expression, $where->getExpression());
    }

    public function testAddExpressionReturnsSameInstance(): void
    {
        $expected = new WhereClause();
        $actual = $expected->addExpression(Literal::boolean(true));

        $this->assertSame($expected, $actual);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new WhereClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
