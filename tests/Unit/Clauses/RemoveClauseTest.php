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
use TypeError;
use WikibaseSolutions\CypherDSL\Clauses\RemoveClause;
use WikibaseSolutions\CypherDSL\Expressions\Label;
use WikibaseSolutions\CypherDSL\Expressions\Property;
use WikibaseSolutions\CypherDSL\Expressions\Variable;
use WikibaseSolutions\CypherDSL\Query;

/**
 * @covers \WikibaseSolutions\CypherDSL\Clauses\RemoveClause
 */
final class RemoveClauseTest extends TestCase
{
    public function testEmptyClause(): void
    {
        $remove = new RemoveClause();

        $this->assertSame("", $remove->toQuery());
    }

    public function testSingleExpression(): void
    {
        $remove = new RemoveClause();
        $expression = new Property(new Variable('Foo'), 'Bar');

        $remove->addExpression($expression);

        $this->assertSame("REMOVE Foo.Bar", $remove->toQuery());
    }

    public function testMultipleExpressionsSingleCall(): void
    {
        $remove = new RemoveClause();

        $a = new Property(new Variable('Foo'), 'Bar');
        $b = new Label(new Variable('a'), 'B');

        $remove->addExpression($a, $b);

        $this->assertSame("REMOVE Foo.Bar, a:B", $remove->toQuery());
    }

    public function testMultipleExpressionsMultipleCalls(): void
    {
        $remove = new RemoveClause();

        $a = new Property(new Variable('Foo'), 'Bar');
        $b = new Label(new Variable('a'), 'B');

        $remove->addExpression($a);
        $remove->addExpression($b);

        $this->assertSame("REMOVE Foo.Bar, a:B", $remove->toQuery());
    }

    public function testAddExpressionReturnsSameInstance(): void
    {
        $expected = new RemoveClause();
        $actual = $expected->addExpression(Query::variable('a')->property('b'));

        $this->assertSame($expected, $actual);
    }

    public function testGetExpressions(): void
    {
        $remove = new RemoveClause();

        $this->assertSame([], $remove->getExpressions());

        $a = Query::variable('a')->property('a');

        $remove->addExpression($a);

        $this->assertSame([$a], $remove->getExpressions());

        $b = Query::variable('a')->property('b');

        $remove->addExpression($b);

        $this->assertSame([$a, $b], $remove->getExpressions());
    }

    public function testAddExpressionThrowsExceptionOnInvalidType(): void
    {
        $remove = new RemoveClause();

        $this->expectException(TypeError::class);

        // @phpstan-ignore-next-line
        $remove->addExpression(10);
    }

    public function testCanBeEmpty(): void
    {
        $clause = new RemoveClause();
        $this->assertFalse($clause->canBeEmpty());
    }
}
